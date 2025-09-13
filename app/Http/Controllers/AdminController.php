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
    /**
     * List all organizations with optional filtering by name, email, location, or industry.
     * Supports GET 'search' query param for filtering.
     */
    /**
     * List all organizations with optional filtering and pagination.
     * Supports GET 'search' query param for filtering.
     * Metrics can be calculated in Blade or here as needed.
     */
    public function organizations(Request $request) {
        $query = Organization::query();
        // If a search term is provided, filter by name, email, location, or industry
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('contact_email', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%")
                  ->orWhere('industry', 'like', "%$search%");
            });
        }
        // Paginate results (10 per page)
        $organizations = $query->orderBy('name')->paginate(10)->withQueryString();
        // Pass organizations to the view (metrics handled in Blade)
        return view('admin.organizations.index', compact('organizations'));
    }

    public function createOrganization() {
        return view('admin.organizations.create');
    }

    public function storeOrganization(Request $r) {
        $r->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'location' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'profile' => 'nullable|string',
        ]);
        $org = Organization::create($r->only(['name','contact_email','location','industry','website','contact_phone','profile']));
        return redirect()->route('admin.organizations.index')->with('status', 'Organization created successfully!');
    }

    public function showOrganization($id) {
        return view('admin.organizations.show', ['organization' => Organization::findOrFail($id)]);
    }

    public function editOrganization($id) {
        return view('admin.organizations.edit', ['organization' => Organization::findOrFail($id)]);
    }

    public function updateOrganization(Request $r, $id) {
        $r->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'location' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'profile' => 'nullable|string',
        ]);
        $org = Organization::findOrFail($id);
        $org->update($r->only(['name','contact_email','location','industry','website','contact_phone','profile']));
        return redirect()->route('admin.organizations.show', $org->id)->with('status', 'Organization updated successfully!');
    }

    public function deleteOrganization($id) {
        $org = Organization::findOrFail($id);
        $org->delete();
        return redirect()->route('admin.organizations.index')->with('status', 'Organization deleted successfully!');
    }

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
