<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrgApplicationsExport;
use Barryvdh\DomPDF\Facade\Pdf;


class ApplicationController extends Controller
{
    /**
     * Export organization applications as Excel or PDF.
     */
    public function exportApplications($type)
    {
        $user = Auth::user();
        if ($user->role !== 'organization') abort(403);
        $organization = $user->organizationProfile;
        $applications = \App\Models\Application::whereHas('opportunity', function($q) use ($organization) {
            $q->where('organization_id', $organization->id ?? null);
        })->with(['student.user', 'opportunity.skills', 'student.skills'])->get();
        if ($type === 'excel') {
            return Excel::download(new OrgApplicationsExport, 'organization_applications.xlsx');
        } elseif ($type === 'pdf') {
            $orgName = $organization->name ?? 'Organization';
            $pdf = Pdf::loadView('applications.export_pdf', compact('applications', 'orgName'));
            return $pdf->download('organization_applications.pdf');
        }
        abort(404);
    }
    /**
     * Student manually recalculates and persists ML scores for their own application.
     */
    public function studentCalculateMatch(Request $request, $id)
    {
        $user = Auth::user();
        $application = Application::with(['student', 'student.user', 'opportunity'])->findOrFail($id);
        // Only allow if this is the logged-in student's application
        if (!$user || $user->role !== 'student' || $application->student_id !== $user->student->id) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }
        $matcher = new \App\Services\SkillMatcher();
        $studentSkills = $application->student->skills->pluck('name')->toArray();
        $opportunitySkills = $application->opportunity->skills->pluck('name')->toArray();
        $studentProfile = ($application->student->bio ?? '') . ' ' . ($application->student->interests ?? '') . ' ' . ($application->student->course ?? '') . ' ' . ($application->student->university ?? '');
        $opportunityProfile = ($application->opportunity->description ?? '') . ' ' . ($application->opportunity->title ?? '');
        $studentText = implode(' ', $studentSkills) . ' ' . $studentProfile;
        $opportunityText = implode(' ', $opportunitySkills) . ' ' . $opportunityProfile;
    $mlScores = $matcher->getMLScores($studentText, $opportunityText);
    $application->match_score = $mlScores['rf'];
    $application->tfidf_score = $mlScores['tfidf'];
    $application->logreg_score = $mlScores['logreg'];
    $application->rf_score = $mlScores['rf'];
    $matchedSkills = $matcher->getMatchedSkills($opportunitySkills, $studentSkills);
    $missingSkills = $matcher->getMissingSkills($opportunitySkills, $studentSkills);
    $application->matched_skills = json_encode($matchedSkills);
    $application->missing_skills = json_encode($missingSkills);
    // Gather profile factors for XAI
    $studentProfileData = [
        'course' => $application->student->course ?? '',
        'academic_field' => $application->student->academic_field ?? '',
        'gpa' => $application->student->gpa ?? '',
        'interests' => $application->student->interests ?? '',
    ];
    $opportunityProfileData = [
        'course' => $application->opportunity->course ?? '',
        'academic_field' => $application->opportunity->academic_field ?? '',
        'min_gpa' => $application->opportunity->min_gpa ?? '',
        'interests' => $application->opportunity->interests ?? '',
    ];
    // Pass profile factors directly for improved XAI
    $application->match_message = $matcher->getMatchMessage(
        $mlScores['rf'],
        $matchedSkills,
        $missingSkills,
        $opportunitySkills,
        $application->student->course ?? null,
        $application->student->academic_field ?? null,
        $application->student->gpa ?? null,
        $application->student->interests ? (is_array($application->student->interests) ? $application->student->interests : explode(',', $application->student->interests)) : null
    );
    $application->save();
        return redirect()->back()->with('success', 'ML match score recalculated and saved for your application!');
    }
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

        // Do NOT recalculate ML scores on every page load. Use stored values only.
        return view('applications.index', compact('applications'));
    }

    // Show a single application
    public function show($id)
    {
        $application = Application::with(['student.user', 'opportunity', 'student.skills', 'opportunity.skills'])->findOrFail($id);
        $user = Auth::user();

        // Always recalculate XAI message for display context
        $matcher = new \App\Services\SkillMatcher();
        $studentSkills = $application->student && $application->student->skills ? $application->student->skills->pluck('name')->toArray() : [];
        $opportunitySkills = $application->opportunity && $application->opportunity->skills ? $application->opportunity->skills->pluck('name')->toArray() : [];
        $application->matched_skills = $matcher->getMatchedSkills($opportunitySkills, $studentSkills);
        $application->missing_skills = $matcher->getMissingSkills($opportunitySkills, $studentSkills);
        $application->match_message = $matcher->getMatchMessage(
            $application->rf_score ?? $application->match_score ?? 0,
            $application->matched_skills,
            $application->missing_skills,
            $opportunitySkills,
            $application->student->course ?? null,
            $application->student->academic_field ?? null,
            $application->student->gpa ?? null,
            $application->student->interests ? (is_array($application->student->interests) ? $application->student->interests : explode(',', $application->student->interests)) : null,
            $user->role ?? null
        );

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
            // Map 'interest' from the form to 'interests' for validation
            $requestData = $request->all();
            if (isset($requestData['interest'])) {
                $requestData['interests'] = $requestData['interest'];
            }
            // Adjust validation rules based on the use_existing_resume field
            $rules = [
                'opportunity_id' => 'required|uuid|exists:opportunities,id',
                'skills' => 'required|array',
                'skills.*' => 'uuid|exists:skills,id',
                'interests' => 'required|string',
                'gpa' => 'required|string',
            ];

            if ($request->input('use_existing_resume') !== '1') {
                $rules['resume'] = 'required|file|mimes:pdf,doc,docx|max:2048';
            }

            $validated = Validator::make($requestData, $rules)->validate();
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
        $application->interests = $validated['interests'];
        $application->gpa = $validated['gpa'];

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
        $application->status = 'accepted'; // Use valid enum value
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
    /**
     * Manually recalculate ML match and XAI for any authorized user (student, org, admin).
     */
    public function calculateMatch(Request $request, $id)
    {
        $user = Auth::user();
        $application = Application::with(['student', 'student.user', 'opportunity'])->findOrFail($id);
        // Only allow if student owns, org owns, or admin
        $isStudent = $user->role === 'student' && $application->student_id === optional($user->student)->id;
        $isOrg = $user->role === 'organization' && $application->opportunity->organization_id === optional($user->organizationProfile)->id;
        $isAdmin = $user->role === 'admin';
        if (!($isStudent || $isOrg || $isAdmin)) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }
    $matcher = new \App\Services\SkillMatcher();
    $student = $application->student;
    $opportunity = $application->opportunity;
    $studentSkills = $student->skills->pluck('name')->toArray();
    $opportunitySkills = $opportunity->skills->pluck('name')->toArray();
    // Build comprehensive student profile string
    $studentProfile = implode(' ', [
        $student->bio ?? '',
        $student->interests ?? '',
        $student->activities ?? '',
        $student->course ?? '',
        $student->year ?? '',
        $student->university ?? '',
        $student->academic_field ?? '',
        $student->gpa ?? '',
        $student->is_available ? 'available' : 'not available',
        $student->rating ?? '',
        $student->rating_count ?? '',
        $student->resume ?? '',
        $student->organizations ?? ''
    ]);
    // Build comprehensive opportunity profile string
    $opportunityProfile = implode(' ', [
        $opportunity->description ?? '',
        $opportunity->title ?? '',
        $opportunity->industry ?? '',
        $opportunity->min_gpa ?? '',
        $opportunity->interests ?? '',
        $opportunity->activities ?? '',
        $opportunity->organizations ?? '',
        $opportunity->course ?? '',
        $opportunity->academic_field ?? ''
    ]);
    $studentText = implode(' ', $studentSkills) . ' ' . $studentProfile;
    $opportunityText = implode(' ', $opportunitySkills) . ' ' . $opportunityProfile;
    $mlScores = $matcher->getMLScores($studentText, $opportunityText);
    $application->match_score = $mlScores['rf'];
    $application->tfidf_score = $mlScores['tfidf'];
    $application->logreg_score = $mlScores['logreg'];
    $application->rf_score = $mlScores['rf'];
    // Add XAI fields for immediate display
    $application->matched_skills = $matcher->getMatchedSkills($opportunitySkills, $studentSkills);
    $application->missing_skills = $matcher->getMissingSkills($opportunitySkills, $studentSkills);
    $application->match_message = $matcher->getMatchMessage(
        $mlScores['rf'],
        $application->matched_skills,
        $application->missing_skills,
        $opportunitySkills,
        $student->course ?? null,
        $student->academic_field ?? null,
        $student->gpa ?? null,
        $student->interests ? (is_array($student->interests) ? $student->interests : explode(',', $student->interests)) : null
    );
    $application->xai_course = $student->course ?? '';
    $application->xai_academic_field = $student->academic_field ?? '';
    $application->xai_gpa = $student->gpa ?? '';
    $application->xai_interests = $student->interests ?? '';
    $application->save();
    return redirect()->back()->with('success', 'ML match score and XAI recalculated and saved for this application!');
    }


}
