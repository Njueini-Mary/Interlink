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
use App\Models\Skill;
use App\Models\Category;

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
    public function updateUser(Request $r, $id) {
        $user = User::findOrFail($id);
        $user->update($r->all());
        return redirect()->route('admin.users.show', $user->id)->with('success', 'User updated successfully.');
    }
    /** Delete user. */
    public function deleteUser($id) {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

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
    /**
     * Show paginated list of students for admin view.
     * Uses Eloquent pagination for correct numbering and navigation.
     * Filters/search can be added via query parameters.
     */
    /**
     * Show paginated and filtered list of students for admin view.
     * Supports search by name, university, course, and filter by year/university.
     */
    public function students(Request $r) {
        $query = Student::with('user');
        if ($r->filled('search')) {
            $search = $r->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%$search%") ;
                })
                ->orWhere('university', 'like', "%$search%")
                ->orWhere('course', 'like', "%$search%") ;
            });
        }
        if ($r->filled('year')) {
            $query->where('year', $r->input('year'));
        }
        if ($r->filled('university')) {
            $query->where('university', $r->input('university'));
        }
        $students = $query->paginate(10)->appends($r->except('page'));
        return view('admin.students.index', compact('students'));
    }
    public function showStudent($id) { return view('admin.students.show', ['student' => Student::findOrFail($id)]); }
    public function editStudent($id) { return view('admin.students.edit', ['student' => Student::findOrFail($id)]); }
    public function updateStudent(Request $r, $id) {
        $student = Student::findOrFail($id);
        $student->update($r->all());
        return redirect()->route('admin.students.show', $student->id)->with('success', 'Student updated successfully.');
    }
    public function deleteStudent($id) {
        $student = Student::findOrFail($id);
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }

    // --- CRUD for Opportunities ---
    /**
     * Show paginated list of opportunities for admin view.
     * Uses Eloquent pagination for correct numbering and navigation.
     */
    /**
     * Show paginated and filtered list of opportunities for admin view.
     * Supports search by title, organization, and filter by type/organization.
     */
    public function opportunities(Request $r) {
        $query = Opportunity::with('organization');
        if ($r->filled('search')) {
            $search = $r->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhereHas('organization', function($qu) use ($search) {
                      $qu->where('name', 'like', "%$search%") ;
                  });
            });
        }
        if ($r->filled('type')) {
            $query->where('type', $r->input('type'));
        }
        if ($r->filled('organization')) {
            $query->whereHas('organization', function($q) use ($r) {
                $q->where('name', $r->input('organization'));
            });
        }
        $opportunities = $query->paginate(10)->appends($r->except('page'));
        return view('admin.opportunities.index', compact('opportunities'));
    }
    public function showOpportunity($id) { return view('admin.opportunities.show', ['opportunity' => Opportunity::findOrFail($id)]); }
    public function editOpportunity($id)
    {
        $opportunity = Opportunity::with(['organization', 'skills', 'category'])->findOrFail($id);
        $skills = Skill::all();
        $organizations = Organization::all();
        $categories = Category::all();

        return view('admin.opportunities.edit', [
            'opportunity' => $opportunity,
            'skills' => $skills,
            'organizations' => $organizations,
            'categories' => $categories,
            'showId' => false, // Added to control ID visibility
        ]);
    }
    /**
     * Update an opportunity with validated data.
     * 
     * Admin can update opportunity details including title, type, description,
     * skills, organization, category, and compensation information.
     * 
     * @param Request $request HTTP request containing opportunity data
     * @param string $id UUID of the opportunity to update
     * @return \Illuminate\Http\RedirectResponse Redirect to opportunity show page
     */
    public function updateOpportunity(Request $request, $id)
    {
        // Retrieve the opportunity by ID
        $opportunity = Opportunity::findOrFail($id);

        // Validate the incoming request data
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:WBL,SBL'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'status' => ['nullable', 'in:open,closed,filled'],
            'compensation_type' => ['nullable', 'in:monthly,hourly'],
            'monthly_stipend' => ['nullable', 'numeric', 'min:0'],
            'min_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'max_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:8'],
        ]);

        // Update the opportunity with validated data
        $opportunity->update($validated);

        // Log the update action
        \Illuminate\Support\Facades\Log::info('Admin updated opportunity', [
            'opportunity_id' => $opportunity->id,
            'admin_id' => auth()->id(),
        ]);

        // Redirect to the opportunity detail page with success message
        return redirect()->route('opportunities.show', $opportunity->id)
                         ->with('success', 'Opportunity updated successfully.');
    }

    /**
     * Delete an opportunity from the database.
     * 
     * Admin can permanently remove an opportunity. This action also removes
     * associated applications and related records as per cascade rules.
     * 
     * @param string $id UUID of the opportunity to delete
     * @return \Illuminate\Http\RedirectResponse Redirect to opportunities listing
     */
    public function deleteOpportunity($id)
    {
        try {
            // Find the opportunity by ID
            $opportunity = Opportunity::findOrFail($id);
            
            // Store opportunity title for logging and feedback
            $opportunityTitle = $opportunity->title;
            $opportunityId = $opportunity->id;

            // Delete all applications associated with this opportunity
            // Note: If you have cascade delete set in your database, this might be automatic
            $applicationsCount = $opportunity->applications()->count();
            $opportunity->applications()->delete();

            // Detach all related skills (many-to-many relationship)
            $opportunity->skills()->detach();

            // Delete the opportunity itself
            $opportunity->delete();

            // Log the deletion activity for audit trail
            \Illuminate\Support\Facades\Log::info('Admin deleted opportunity', [
                'opportunity_id' => $opportunityId,
                'opportunity_title' => $opportunityTitle,
                'applications_deleted' => $applicationsCount,
                'admin_id' => auth()->id(),
                'deleted_at' => now(),
            ]);

            // Redirect to opportunities index with success message
            return redirect()->route('admin.opportunities.index')
                ->with('success', "Opportunity '{$opportunityTitle}' and {$applicationsCount} related applications deleted successfully.");

        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Failed to delete opportunity', [
                'opportunity_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
            ]);

            // Redirect back with error message
            return back()->withErrors(['error' => 'Failed to delete opportunity. Please try again.']);
        }
    }

    // --- CRUD for Applications ---
    /**
     * Show paginated list of applications for admin view.
     * Uses Eloquent pagination for correct numbering and navigation.
     */
    /**
     * Show paginated and filtered list of applications for admin view.
     * Supports search by student, opportunity, and filter by status.
     */
    public function applications(Request $r) {
        $query = Application::with(['student.user', 'opportunity']);
        if ($r->filled('search')) {
            $search = $r->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('student.user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%$search%") ;
                })
                ->orWhereHas('opportunity', function($qu) use ($search) {
                    $qu->where('title', 'like', "%$search%") ;
                });
            });
        }
        if ($r->filled('status')) {
            $query->where('status', $r->input('status'));
        }
        $applications = $query->paginate(10)->appends($r->except('page'));
        return view('admin.applications.index', compact('applications'));
    }
    public function showApplication($id) { return view('admin.applications.show', ['application' => Application::findOrFail($id)]); }
    public function editApplication($id) { return view('admin.applications.edit', ['application' => Application::findOrFail($id)]); }
    public function updateApplication(Request $r, $id) {
        $application = Application::findOrFail($id);
        $application->update($r->all());
        return redirect()->route('admin.applications.show', $application->id)->with('success', 'Application updated successfully.');
    }
    public function deleteApplication($id) {
        $application = Application::findOrFail($id);
        $application->delete();
        return redirect()->route('admin.applications.index')->with('success', 'Application deleted successfully.');
    }

    // --- CRUD for Notifications ---
    /**
     * Show paginated list of notifications for admin view.
     * Uses Eloquent pagination for correct numbering and navigation.
     */
    /**
     * Show paginated and filtered list of notifications for admin view.
     * Supports search by recipient, message, and filter by type.
     */
    public function notifications(Request $r) {
        $query = Notification::with('notifiable');
        if ($r->filled('search')) {
            $search = $r->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('notifiable', function($qu) use ($search) {
                    $qu->where('name', 'like', "%$search%") ;
                })
                ->orWhere('data->message', 'like', "%$search%") ;
            });
        }
        if ($r->filled('type')) {
            $query->where('type', $r->input('type'));
        }
        $notifications = $query->paginate(10)->appends($r->except('page'));
        return view('admin.notifications.index', compact('notifications'));
    }
    public function showNotification($id) { return view('admin.notifications.show', ['notification' => Notification::findOrFail($id)]); }
    public function editNotification($id) { return view('admin.notifications.edit', ['notification' => Notification::findOrFail($id)]); }
    public function updateNotification(Request $r, $id) {
        $notification = Notification::findOrFail($id);
        $notification->update($r->all());
        return redirect()->route('admin.notifications.show', $notification->id)->with('success', 'Notification updated successfully.');
    }
    public function deleteNotification($id) {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted successfully.');
    }

    // --- CRUD for Activities ---
    /**
     * Show paginated list of activities for admin view.
     * Uses Eloquent pagination for correct numbering and navigation.
     */
    /**
     * Show paginated and filtered list of activities for admin view.
     * Supports search by user, target, and filter by action.
     */
    public function activities(Request $r) {
        $query = Activity::with('user');
        if ($r->filled('search')) {
            $search = $r->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%$search%") ;
                })
                ->orWhere('target_type', 'like', "%$search%")
                ->orWhere('target_id', 'like', "%$search%") ;
            });
        }
        if ($r->filled('action')) {
            $query->where('action', $r->input('action'));
        }
        $activities = $query->paginate(10)->appends($r->except('page'));
        return view('admin.activities.index', compact('activities'));
    }
    public function showActivity($id) { return view('admin.activities.show', ['activity' => Activity::findOrFail($id)]); }
    public function editActivity($id) { return view('admin.activities.edit', ['activity' => Activity::findOrFail($id)]); }
    public function updateActivity(Request $r, $id) {
        $activity = Activity::findOrFail($id);
        $activity->update($r->all());
        return redirect()->route('admin.activities.show', $activity->id)->with('success', 'Activity updated successfully.');
    }
    public function deleteActivity($id) {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        return redirect()->route('admin.activities.index')->with('success', 'Activity deleted successfully.');
    }
}
