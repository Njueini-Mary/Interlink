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
            $applications = Application::where('student_id', $user->student->id)->with('opportunity')->get();
            return view('applications.index', compact('applications'));
        } elseif ($user->role === 'organization') {
            if (!$user->organizationProfile) {
                return view('applications.index', ['applications' => collect(), 'profileMissing' => true]);
            }
            $applications = Application::whereHas('opportunity', function($q) use ($user) {
                $q->where('organization_id', $user->organizationProfile->id);
            })->with('student')->get();
            return view('applications.index', compact('applications'));
        }
        abort(403);
    }

    // Show a single application
    public function show($id)
    {
        $application = Application::with(['student.user', 'opportunity'])->findOrFail($id);
        $user = Auth::user();

        if (
            ($user->role === 'student' && optional($application->student)->id === optional($user->student)->id) ||
            ($user->role === 'organization' && optional($application->opportunity)->organization_id === optional($user->organization)->id)
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
}
