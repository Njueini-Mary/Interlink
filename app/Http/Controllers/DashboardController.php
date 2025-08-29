<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;
use App\Models\Organization;

class DashboardController extends Controller
{
    /**
     * Redirect user to their role-specific dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'organization') {
            return redirect()->route('organization.dashboard');
        } elseif ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }
        return $this->defaultDashboard();
    }
    /**
     * About page
     */
    /**
     * Show the about page.
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('about.index');
    }
    /**
     * Show the student dashboard with stats and recent applications.
     *
     * @return \Illuminate\View\View
     */
    public function studentDashboard()
    {
        $user = Auth::user();
    $student = $user->student;
        $data = [
            'activeOpportunities' => \App\Models\Opportunity::where('status', 'open')->count(),
            'applicationsSubmitted' => $student ? $student->applications()->count() : 0,
            'interviewsScheduled' => $student ? $student->applications()->where('status', 'interview')->count() : 0,
            'placementsSecured' => $student ? $student->applications()->where('status', 'placed')->count() : 0,
            'applications' => $student ? $student->applications()->with('opportunity.organization')->latest()->take(5)->get() : [],
        ];
        return view('dashboards.student', $data);
    }

    /**
     * Show the organization dashboard with stats, opportunities, and applications.
     *
     * @return \Illuminate\View\View
     */
    public function organizationDashboard()
    {
        $user = Auth::user();
        $org = $user->organizationProfile;
        $opportunities = $org ? $org->opportunities()->withCount('applications')->latest()->take(5)->get() : [];
        $applications = collect();
        if ($org) {
            foreach ($opportunities as $opp) {
                $applications = $applications->merge($opp->applications()->latest()->take(3)->get());
            }
        }
        $data = [
            'openOpportunities' => $org ? $org->opportunities()->where('status', 'open')->count() : 0,
            'placementsSecured' => $org ? $org->opportunities()->where('status', 'placed')->count() : 0,
            'rating' => $org ? $org->rating : null,
            'opportunities' => $opportunities,
            'applications' => $applications,
        ];
        return view('dashboards.organization', $data);
    }
    
    /**
     * Show the admin dashboard with system-wide stats and recent users.
     *
     * @return \Illuminate\View\View
     */
    public function adminDashboard()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $data = [
            'totalUsers' => User::count(),
            'newUsersThisMonth' => User::where('created_at', '>=', $startOfMonth)->count(),
            'activeStudents' => User::role('student')->whereHas('student', function($q) {
                $q->where('is_available', true);
            })->count(),
            'applicationsSubmitted' => \App\Models\Application::count(),
            'activeOpportunities' => \App\Models\Opportunity::where('status', 'open')->count(),
            'placementsSecured' => \App\Models\Opportunity::where('status', 'placed')->count(),
            'totalOrganizations' => Organization::count(),
            'recentUsers' => User::with(['roles'])->latest()->take(5)->get(),
            'recentActivities' => \App\Models\Activity::latest()->take(10)->get(),
            'systemNotifications' => \App\Models\Notification::latest()->take(5)->get(),
            'recentCategories' => \App\Models\Category::latest()->take(5)->get(),
            'recentReviews' => \App\Models\Review::latest()->take(5)->get(),
            'recentSkills' => \App\Models\Skill::latest()->take(5)->get(),
            'recentTasks' => \App\Models\Task::latest()->take(5)->get(),
            'recentTimeLogs' => \App\Models\TimeLog::latest()->take(5)->get(),
        ];
        return view('dashboards.admin', $data);
    }

    /**
     * Show a default dashboard for users without a specific role.
     *
     * @return \Illuminate\View\View
     */
    public function defaultDashboard()
    {
        return view('dashboard', ['role' => 'user']);
    }
}