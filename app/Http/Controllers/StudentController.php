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
        $student = auth()->user()->student;
        $opportunities = \App\Models\Opportunity::where('status', 'open')->paginate(10);
        return view('students.opportunities', compact('opportunities', 'student'));
    }

    // Show student's applications
    public function applications(Request $request)
    {
        $student = auth()->user()->student;
        $applications = \App\Models\Application::where('student_id', $student->id)->latest()->paginate(10);
        return view('students.applications', compact('applications', 'student'));
    }

    // Show student's interviews
    public function interviews(Request $request)
    {
        $student = auth()->user()->student;
        $interviews = \App\Models\Application::where('student_id', $student->id)->where('status', 'interview')->latest()->paginate(10);
        return view('students.interviews', compact('interviews', 'student'));
    }

    // Show student's placements
    public function placements(Request $request)
    {
        $student = auth()->user()->student;
        $placements = \App\Models\Application::where('student_id', $student->id)->where('status', 'placed')->latest()->paginate(10);
        return view('students.placements', compact('placements', 'student'));
    }
}