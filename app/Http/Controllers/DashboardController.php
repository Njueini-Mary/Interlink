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
     * Show the student dashboard with stats, recent applications, and ML-recommended opportunities.
     *
     * This is the central feature for final year project: ML-based opportunity matching
     * 
     * @return \Illuminate\View\View
     */
    public function studentDashboard()
    {
        $user = Auth::user();
        $student = $user->student;
        
        // Initialize ML matcher
        $mlMatcher = new \App\Services\SkillMatcher();
        
        // Get ML-recommended opportunities for this student
        $recommendedOpportunities = [];
        $mlModelStatus = $mlMatcher->isMLModelAvailable();
        
        if ($student) {
            // Get all open opportunities with skills loaded
            $openOpportunities = \App\Models\Opportunity::where('status', 'open')
                ->with(['skills', 'organization'])
                ->get();
            
            // Get ML matches (top 5 for dashboard)
            $allMatches = $mlMatcher->batchMatch($student, $openOpportunities);
            $recommendedOpportunities = array_slice($allMatches, 0, 5);
            
            // Log ML matching for analytics
            \Illuminate\Support\Facades\Log::info('Student Dashboard ML Matching', [
                'student_id' => $student->id,
                'total_opportunities' => $openOpportunities->count(),
                'recommended_count' => count($recommendedOpportunities),
                'ml_model_active' => $mlModelStatus
            ]);
        }
        
        // Compile dashboard data
        $data = [
            // Stats
            'activeOpportunities' => \App\Models\Opportunity::where('status', 'open')->count(),
            'applicationsSubmitted' => $student ? $student->applications()->count() : 0,
            'interviewsScheduled' => $student ? $student->applications()->where('status', 'interview')->count() : 0,
            'placementsSecured' => $student ? $student->applications()->where('status', 'placed')->count() : 0,
            
            // Recent Applications
            'applications' => $student ? $student->applications()->with('opportunity.organization')->latest()->take(5)->get() : [],
            
            // ML Recommendations (Innovation for Defense)
            'recommendedOpportunities' => $recommendedOpportunities,
            'mlModelActive' => $mlModelStatus,
            
            // ML Model Info (for showcasing in defense)
            'mlMetadata' => $mlMatcher->getModelMetadata(),
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
        $opportunities = $org ? $org->opportunities()->withCount('applications')->with('skills')->latest()->take(5)->get() : [];
        
        $applications = collect();
        $matcher = new \App\Services\SkillMatcher();
        
        if ($org) {
            foreach ($opportunities as $opp) {
                $oppApplications = $opp->applications()->with('student.skills')->latest()->take(10)->get();
                
                // Add ML match scores to applications
                $oppSkills = $opp->skills->pluck('name')->toArray();
                $oppProfile = $opp->title . ' ' . $opp->description;
                
                foreach ($oppApplications as $app) {
                    if ($app->student) {
                        $studentSkills = $app->student->skills->pluck('name')->toArray();
                        $studentProfile = $app->student->course . ' ' . ($app->student->academic_field ?? '');
                        
                        $app->match_score = $matcher->getMatchPercentage(
                            $oppSkills,
                            $studentSkills,
                            $studentProfile,
                            $oppProfile
                        );
                    }
                }
                
                // Sort by match score
                $oppApplications = $oppApplications->sortByDesc('match_score');
                $applications = $applications->merge($oppApplications->take(3));
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