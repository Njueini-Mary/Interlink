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
     * Manually calculate ML match scores for organization dashboard applications.
     */
    public function calculateOrgMatches(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'organization' || !$user->organizationProfile) {
            return redirect()->back()->with('error', 'Only organizations can calculate matches.');
        }
        $org = $user->organizationProfile;
        $mlMatcher = new \App\Services\SkillMatcher();
        $opportunities = $org->opportunities()->with(['skills', 'applications.student.skills'])->get();
        foreach ($opportunities as $opp) {
            $oppSkills = $opp->skills->pluck('name')->toArray();
            $oppProfile = ($opp->title ?? '') . ' ' . ($opp->description ?? '');
            foreach ($opp->applications as $app) {
                if ($app->student) {
                    $studentSkills = $app->student->skills->pluck('name')->toArray();
                    $studentProfile = ($app->student->bio ?? '') . ' ' . ($app->student->interests ?? '') . ' ' . ($app->student->course ?? '') . ' ' . ($app->student->university ?? '');
                    $studentText = implode(' ', $studentSkills) . ' ' . $studentProfile;
                    $opportunityText = implode(' ', $oppSkills) . ' ' . $oppProfile;
                    $mlScores = $mlMatcher->getMLScores($studentText, $opportunityText);
                    $app->match_score = $mlScores['rf'];
                    $app->tfidf_score = $mlScores['tfidf'];
                    $app->logreg_score = $mlScores['logreg'];
                    $app->rf_score = $mlScores['rf'];
                    $app->save();
                }
            }
        }
        return redirect()->back()->with('success', 'ML matches calculated for organization dashboard!');
    }
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
            
            // ML matches will be calculated manually via button
            
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
                $oppApplications = $opp->applications()->with(['student.skills', 'opportunity.skills'])->latest()->take(10)->get();
                foreach ($oppApplications as $app) {
                    // Add XAI fields if scores exist
                    if ($app->student && isset($app->match_score)) {
                        $oppSkills = $opp->skills->pluck('name')->toArray();
                        $studentSkills = $app->student->skills->pluck('name')->toArray();
                        $course = $app->student->course ?? '';
                        $academicField = $app->student->academic_field ?? '';
                        $gpa = $app->gpa ?? '';
                        $interests = $app->interests ?? $app->student->interests ?? '';
                        $app->matched_skills = $matcher->getMatchedSkills($oppSkills, $studentSkills);
                        $app->missing_skills = $matcher->getMissingSkills($oppSkills, $studentSkills);
                        $app->match_message = $matcher->getMatchMessage(
                            $app->match_score,
                            $app->matched_skills,
                            $app->missing_skills,
                            $oppSkills,
                            $course,
                            $academicField,
                            $gpa,
                            $interests ? (is_array($interests) ? $interests : explode(',', $interests)) : null,
                            'organization'
                        );
                        $app->xai_course = $course;
                        $app->xai_academic_field = $academicField;
                        $app->xai_gpa = $gpa;
                        $app->xai_interests = $interests;
                    }
                }
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

        /**
     * Manually calculate ML match scores for student dashboard.
     */
    public function calculateMatches(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'student' || !$user->student) {
            return redirect()->back()->with('error', 'Only students can calculate matches.');
        }
        $mlMatcher = new \App\Services\SkillMatcher();
        $student = $user->student;
        $openOpportunities = \App\Models\Opportunity::where('status', 'open')
            ->with(['skills', 'organization'])
            ->get();
        $allMatches = $mlMatcher->batchMatch($student, $openOpportunities);
        // Optionally store matches in session or cache for display
        session(['student_dashboard_matches' => $allMatches]);
        return redirect()->back()->with('success', 'ML matches calculated!');
    }

}