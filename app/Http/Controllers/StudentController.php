<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * StudentController manages student listing, details, and related actions.
 *
 * - Lists students with categories and popular categories.
 * - Shows individual student profiles.
 */

class StudentController extends Controller
{
    /**
     * Manually calculate ML match scores for student applications.
     */
    public function calculateMatches(Request $request)
    {
    $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user || $user->role !== 'student' || !$user->student) {
            return redirect()->back()->with('error', 'Only students can calculate matches.');
        }
        $mlMatcher = new \App\Services\SkillMatcher();
        $student = $user->student;
        $applications = \App\Models\Application::where('student_id', $student->id)->with(['opportunity.skills', 'student.skills'])->get();
        foreach ($applications as $application) {
            $studentSkills = $application->student->skills->pluck('name')->toArray();
            $opportunitySkills = $application->opportunity->skills->pluck('name')->toArray();
            $studentProfile = ($application->student->bio ?? '') . ' ' . ($application->student->interests ?? '') . ' ' . ($application->student->course ?? '') . ' ' . ($application->student->university ?? '');
            $opportunityProfile = ($application->opportunity->description ?? '') . ' ' . ($application->opportunity->title ?? '');
            $studentText = implode(' ', $studentSkills) . ' ' . $studentProfile;
            $opportunityText = implode(' ', $opportunitySkills) . ' ' . $opportunityProfile;
            $mlScores = $mlMatcher->getMLScores($studentText, $opportunityText);
            $application->match_score = $mlScores['rf'];
            $application->tfidf_score = $mlScores['tfidf'];
            $application->logreg_score = $mlScores['logreg'];
            $application->rf_score = $mlScores['rf'];
            $application->save();
        }
        return redirect()->back()->with('success', 'ML matches calculated for your applications!');
    }
    // List all students
    public function index()
    {
        $students = Student::with('user')->paginate(10);
        $categories = Category::all();
        // Get top 6 categories with most students
        $popularCategories = Category::withCount('students')->orderByDesc('students_count')->take(6)->get();
        // Add fallback icon if not present (for demo)
        foreach ($popularCategories as $cat) {
            if (!isset($cat->icon)) {
                $cat->icon = 'graduation-cap'; // fallback FontAwesome icon
            }
        }
        return view('students.index', compact('students', 'categories', 'popularCategories'));
    }

    // Show a single student
    public function show($id)
    {
    $student = Student::with('user')->findOrFail($id);
    $applications = $student->applications()->with('opportunity')->get();
    return view('students.show', compact('student', 'applications'));
    }

    // Show student's active opportunities
    public function opportunities(Request $request)
    {
    $student = \Illuminate\Support\Facades\Auth::user()->student;
        $opportunities = \App\Models\Opportunity::where('status', 'open')->paginate(10);
        return view('students.opportunities', compact('opportunities', 'student'));
    }

    // Show student's applications
    public function applications(Request $request)
    {
    $student = \Illuminate\Support\Facades\Auth::user()->student;
        $applications = \App\Models\Application::where('student_id', $student->id)->latest()->paginate(10);
        return view('students.applications', compact('applications', 'student'));
    }

    // Show student's interviews
    public function interviews(Request $request)
    {
    $student = \Illuminate\Support\Facades\Auth::user()->student;
        $interviews = \App\Models\Application::where('student_id', $student->id)->where('status', 'interview')->latest()->paginate(10);
        return view('students.interviews', compact('interviews', 'student'));
    }

    // Show student's placements
    public function placements(Request $request)
    {
    $student = \Illuminate\Support\Facades\Auth::user()->student;
        $placements = \App\Models\Application::where('student_id', $student->id)->where('status', 'placed')->latest()->paginate(10);
        return view('students.placements', compact('placements', 'student'));
    }
}