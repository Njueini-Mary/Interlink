<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    // List applications for the logged-in user (student or organization)
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'student') {
            if (!$user->student) {
                return view('applications.index', ['applications' => collect(), 'profileMissing' => true]);
            }
            $applications = Application::where('student_id', $user->student->id)
                ->with(['opportunity.skills', 'student.skills', 'student.user'])
                ->get();
        } elseif ($user->role === 'organization') {
            // Only show applications for opportunities owned by this organization
            $orgId = $user->organizationProfile->id;
            $applications = Application::whereHas('opportunity', function($query) use ($orgId) {
                $query->where('organization_id', $orgId);
            })
                ->with(['student.skills', 'student.user', 'opportunity.skills'])
                ->get();
        } elseif ($user->role === 'admin') {
            $applications = Application::with(['student.skills', 'student.user', 'opportunity.skills'])->get();
        } else {
            abort(403);
        }

        // Add ML match scores to each application
        $matcher = new \App\Services\SkillMatcher();
        foreach ($applications as $application) {
            // Always fetch skill names, not IDs
            $studentSkills = [];
            if ($application->student && $application->student->skills) {
                foreach ($application->student->skills as $skill) {
                    $studentSkills[] = is_object($skill) ? $skill->name : $skill;
                }
            }
            $opportunitySkills = [];
            if ($application->opportunity && $application->opportunity->skills) {
                foreach ($application->opportunity->skills as $skill) {
                    $opportunitySkills[] = is_object($skill) ? $skill->name : $skill;
                }
            }
            // Gather richer profile/description text
            $studentProfile = '';
            if ($application->student) {
                $studentProfile = trim(
                    ($application->student->bio ?? '') . ' '
                    . ($application->student->interests ?? '') . ' '
                    . ($application->student->course ?? '') . ' '
                    . ($application->student->university ?? '')
                );
            }
            $opportunityProfile = '';
            if ($application->opportunity) {
                $opportunityProfile = trim(
                    ($application->opportunity->description ?? '') . ' '
                    . ($application->opportunity->title ?? '')
                );
            }
            // Debug log for skills and profiles
            \Log::debug('Application Skills', [
                'application_id' => $application->id,
                'student_name' => $application->student && $application->student->user ? $application->student->user->name : null,
                'student_skills' => $studentSkills,
                'student_profile' => $studentProfile,
                'opportunity_title' => $application->opportunity ? $application->opportunity->title : null,
                'opportunity_skills' => $opportunitySkills,
                'opportunity_profile' => $opportunityProfile
            ]);
            $studentText = implode(' ', $studentSkills) . ' ' . $studentProfile;
            $opportunityText = implode(' ', $opportunitySkills) . ' ' . $opportunityProfile;
            $mlScores = $matcher->getMLScores($studentText, $opportunityText);
            $application->match_score = $mlScores['rf'];
            $application->tfidf_score = $mlScores['tfidf'];
            $application->logreg_score = $mlScores['logreg'];
            $application->rf_score = $mlScores['rf'];
        }
        return view('applications.index', compact('applications'));
    }

    // Show a single application
    public function show($id)
    {
        $application = Application::with(['student.user', 'opportunity'])->findOrFail($id);
        $user = Auth::user();

        // Add ML match score
        $studentSkills = optional($application->student)->skills ?? [];
        $opportunitySkills = optional($application->opportunity)->skills ?? [];
        $matcher = new \App\Services\SkillMatcher();

        // Fix: Convert Eloquent Collection to array of skill names
        if ($studentSkills instanceof \Illuminate\Database\Eloquent\Collection) {
            $studentSkills = $studentSkills->pluck('name')->toArray();
        } elseif (is_array($studentSkills)) {
            // Already array
        } else {
            $studentSkills = [];
        }
        if ($opportunitySkills instanceof \Illuminate\Database\Eloquent\Collection) {
            $opportunitySkills = $opportunitySkills->pluck('name')->toArray();
        } elseif (is_array($opportunitySkills)) {
            // Already array
        } else {
            $opportunitySkills = [];
        }
        $application->match_score = $matcher->getMatchPercentage($opportunitySkills, $studentSkills);

        if (
            ($user->role === 'student' && optional($application->student)->id === optional($user->student)->id) ||
            ($user->role === 'organization' && optional($application->opportunity)->organization_id === optional($user->organizationProfile)->id) ||
            $user->role === 'admin'
        ) {
            return view('applications.show', compact('application'));
        }

        abort(403);
    }

    /**
     * Store a new application.
     *
     * This method handles the submission of a new application by a student.
     * It validates the incoming request, saves the application data to the database,
     * and optionally uploads the resume file if provided.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing application data.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message upon successful submission.
     */
    public function store(Request $request)
    {
        // Log the incoming request data for debugging purposes.
        \Illuminate\Support\Facades\Log::info('Store method called with data: ', $request->all());

        // Log all input fields for debugging purposes
        \Illuminate\Support\Facades\Log::info('All input fields: ', $request->all());

        try {
            // Adjust validation rules based on the use_existing_resume field
            $rules = [
                'opportunity_id' => 'required|uuid|exists:opportunities,id',
                'cover_letter' => 'required|string',
                'skills' => 'required|array',
                'skills.*' => 'uuid|exists:skills,id',
            ];

            if ($request->input('use_existing_resume') !== '1') {
                $rules['resume'] = 'required|file|mimes:pdf,doc,docx|max:2048';
            }

            $validated = $request->validate($rules);
            \Illuminate\Support\Facades\Log::info('Validated fields: ', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation error: ', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        // Create a new application instance and populate its attributes.
        $application = new Application();
        $application->opportunity_id = $validated['opportunity_id'];
        $application->student_id = Auth::user()->student->id; // Associate the application with the logged-in student.
        $application->cover_letter = $validated['cover_letter'];
        $application->interests = $request->input('interest');
        $application->gpa = $request->input('gpa');

        // Handle resume file upload if provided.
        if ($request->input('use_existing_resume') === '1') {
            $application->resume = Auth::user()->student->resume; // Use the existing resume
        } else if ($request->hasFile('resume')) {
            $application->resume = $request->file('resume')->store('resumes', 'public'); // Store the new resume
            \Illuminate\Support\Facades\Log::info('Resume uploaded to: ' . $application->resume);
        } else {
            \Illuminate\Support\Facades\Log::info('No resume file uploaded.');
        }

        // Ensure the application ID is set
        $application->id = (string) \Illuminate\Support\Str::uuid();

        // Save the application to the database.
        try {
            $application->save();
            // Attach selected skills to student profile if not already present
            $student = Auth::user()->student;
            $student->skills()->syncWithoutDetaching($validated['skills']);
            \Illuminate\Support\Facades\Log::info('Application saved successfully with ID: ' . $application->id); // Log the success.

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving application: ' . $e->getMessage()); // Log any errors.

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your application. Please try again.'
            ], 500);
        }
    }

    /**
     * Approve an application.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $application = Application::findOrFail($id);
        $application->status = 'approved';
        $application->save();

        return redirect()->route('applications.index')->with('success', 'Application approved successfully.');
    }

    /**
     * Reject an application.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $application = Application::findOrFail($id);
        $application->status = 'rejected';
        $application->save();

        return redirect()->route('applications.index')->with('success', 'Application rejected successfully.');
    }

    /**
     * Generate ML match score for an application (admin action)
     */
    public function generateMatch($id)
    {
        $application = Application::with(['student', 'student.user', 'opportunity'])->findOrFail($id);
        $matcher = new \App\Services\SkillMatcher();
        // Fix: Convert Eloquent Collection to array of skill names
        $studentSkills = optional($application->student)->skills;
        if ($studentSkills instanceof \Illuminate\Database\Eloquent\Collection) {
            $studentSkills = $studentSkills->pluck('name')->toArray();
        } elseif (is_array($studentSkills)) {
            // Already array
        } else {
            $studentSkills = [];
        }
        $opportunitySkills = optional($application->opportunity)->skills;
        if ($opportunitySkills instanceof \Illuminate\Database\Eloquent\Collection) {
            $opportunitySkills = $opportunitySkills->pluck('name')->toArray();
        } elseif (is_array($opportunitySkills)) {
            // Already array
        } else {
            $opportunitySkills = [];
        }
        $studentProfile = $application->student ? trim(
            ($application->student->bio ?? '') . ' '
            . ($application->student->interests ?? '') . ' '
            . ($application->student->course ?? '') . ' '
            . ($application->student->university ?? '')
        ) : '';
        $opportunityProfile = $application->opportunity ? trim(
            ($application->opportunity->description ?? '') . ' '
            . ($application->opportunity->title ?? '')
        ) : '';
        $studentText = implode(' ', $studentSkills) . ' ' . $studentProfile;
        $opportunityText = implode(' ', $opportunitySkills) . ' ' . $opportunityProfile;
        $mlScores = $matcher->getMLScores($studentText, $opportunityText);
        $application->match_score = $mlScores['rf'];
        $application->tfidf_score = $mlScores['tfidf'];
        $application->logreg_score = $mlScores['logreg'];
        $application->rf_score = $mlScores['rf'];
        $application->save();
        return redirect()->back()->with('success', 'ML match score generated!');
    }
}
