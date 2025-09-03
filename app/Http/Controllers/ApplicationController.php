<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

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
            ($user->role === 'student' && $application->student_id === $user->student->id) ||
            ($user->role === 'organization' && $application->opportunity->organization_id === $user->organization->id)
        ) {
            return view('applications.show', compact('application'));
        }
        abort(403);
    }
}
