<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;
use App\Models\Student;
use App\Models\Opportunity;
use App\Models\Application;
use App\Models\Notification;
use App\Models\Activity;

/**
 * AdminController
 * Handles all admin metrics, CRUD, and management actions for all major models.
 * All methods are protected by admin middleware in routes.
 */
class AdminController extends Controller
{
    /**
     * Show the admin dashboard with all metrics.
     */
    public function dashboard()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $data = [
            'totalUsers' => User::count(),
            'totalStudents' => User::where('role', 'student')->count(),
            'totalOrganizations' => User::where('role', 'organization')->count(),
            'totalOpportunities' => Opportunity::count(),
            'totalApplications' => Application::count(),
            'totalPlacements' => Opportunity::where('status', 'placed')->count(),
            // 'activeUsers' and 'inactiveUsers' removed due to missing last_login_at column
            'activeUsers' => '-',
            'inactiveUsers' => '-',
            'newUsersThisMonth' => User::where('created_at', '>=', $startOfMonth)->count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentActivities' => Activity::latest()->take(10)->get(),
            'systemNotifications' => Notification::latest()->take(5)->get(),
        ];
        return view('admin.dashboard', $data);
    }

    // --- CRUD for Users ---
    /** List all users. */
    public function users() { return view('admin.users.index', ['users' => User::all()]); }
    /** Show user. */
    public function showUser($id) { return view('admin.users.show', ['user' => User::findOrFail($id)]); }
    /** Edit user. */
    public function editUser($id) { return view('admin.users.edit', ['user' => User::findOrFail($id)]); }
    /** Update user. */
    public function updateUser(Request $r, $id) { /* ... */ }
    /** Delete user. */
    public function deleteUser($id) { /* ... */ }

    // --- CRUD for Organizations ---
    public function organizations() { return view('admin.organizations.index', ['organizations' => Organization::all()]); }
    public function showOrganization($id) { return view('admin.organizations.show', ['organization' => Organization::findOrFail($id)]); }
    public function editOrganization($id) { return view('admin.organizations.edit', ['organization' => Organization::findOrFail($id)]); }
    public function updateOrganization(Request $r, $id) { /* ... */ }
    public function deleteOrganization($id) { /* ... */ }

    // --- CRUD for Students ---
    public function students() { return view('admin.students.index', ['students' => Student::all()]); }
    public function showStudent($id) { return view('admin.students.show', ['student' => Student::findOrFail($id)]); }
    public function editStudent($id) { return view('admin.students.edit', ['student' => Student::findOrFail($id)]); }
    public function updateStudent(Request $r, $id) { /* ... */ }
    public function deleteStudent($id) { /* ... */ }

    // --- CRUD for Opportunities ---
    public function opportunities() { return view('admin.opportunities.index', ['opportunities' => Opportunity::all()]); }
    public function showOpportunity($id) { return view('admin.opportunities.show', ['opportunity' => Opportunity::findOrFail($id)]); }
    public function editOpportunity($id) { return view('admin.opportunities.edit', ['opportunity' => Opportunity::findOrFail($id)]); }
    public function updateOpportunity(Request $r, $id) { /* ... */ }
    public function deleteOpportunity($id) { /* ... */ }

    // --- CRUD for Applications ---
    public function applications() { return view('admin.applications.index', ['applications' => Application::all()]); }
    public function showApplication($id) { return view('admin.applications.show', ['application' => Application::findOrFail($id)]); }
    public function editApplication($id) { return view('admin.applications.edit', ['application' => Application::findOrFail($id)]); }
    public function updateApplication(Request $r, $id) { /* ... */ }
    public function deleteApplication($id) { /* ... */ }

    // --- CRUD for Notifications ---
    public function notifications() { return view('admin.notifications.index', ['notifications' => Notification::all()]); }
    public function showNotification($id) { return view('admin.notifications.show', ['notification' => Notification::findOrFail($id)]); }
    public function editNotification($id) { return view('admin.notifications.edit', ['notification' => Notification::findOrFail($id)]); }
    public function updateNotification(Request $r, $id) { /* ... */ }
    public function deleteNotification($id) { /* ... */ }

    // --- CRUD for Activities ---
    public function activities() { return view('admin.activities.index', ['activities' => Activity::all()]); }
    public function showActivity($id) { return view('admin.activities.show', ['activity' => Activity::findOrFail($id)]); }
    public function editActivity($id) { return view('admin.activities.edit', ['activity' => Activity::findOrFail($id)]); }
    public function updateActivity(Request $r, $id) { /* ... */ }
    public function deleteActivity($id) { /* ... */ }
}
