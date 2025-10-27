<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MLController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\StudentController;

// Auth routes
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');



Route::get('/', function () {
    return view('welcome');
});
// About page
Route::get('/about', [DashboardController::class, 'about'])->name('about');
Route::get('/profile/update-form', [ProfileController::class, 'showUpdateProfileForm'])->name('profile.update.form');

// Profile routes
Route::middleware(['auth'])->group(function () {
    // Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update'); // Change POST to PUT
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// User-facing student routes
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // User-facing application routes
    Route::get('/applications', [\App\Http\Controllers\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{id}', [\App\Http\Controllers\ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/student/applications', [\App\Http\Controllers\ApplicationController::class, 'store'])->name('student.applications.store');
        Route::post('/applications/{id}/calculate-match', [\App\Http\Controllers\ApplicationController::class, 'calculateMatch'])->name('applications.calculate_match');
});




// Admin dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_unless($user && $user->role === 'admin', 403);
        return app(\App\Http\Controllers\DashboardController::class)->adminDashboard();
    })->name('admin.dashboard');

    // Organization dashboard
    Route::get('/organization/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_unless($user && $user->role === 'organization', 403);
        return app(\App\Http\Controllers\DashboardController::class)->organizationDashboard();
    })->name('organization.dashboard');

    // Student dashboard
    Route::get('/student/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_unless($user && $user->role === 'student', 403);
        return app(\App\Http\Controllers\DashboardController::class)->studentDashboard();
    })->name('student.dashboard');
    // Student dashboard metrics links
    Route::get('/student/opportunities', [StudentController::class, 'opportunities'])->name('student.opportunities');
    Route::get('/student/applications', [StudentController::class, 'applications'])->name('student.applications');
    Route::get('/student/interviews', [StudentController::class, 'interviews'])->name('student.interviews');
    Route::get('/student/placements', [StudentController::class, 'placements'])->name('student.placements');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/opportunities/create', [OpportunityController::class, 'create'])->name('opportunities.create');
    Route::post('/opportunities', [OpportunityController::class, 'store'])->name('opportunities.store');
    Route::get('/opportunities', [OpportunityController::class, 'search'])->name('opportunities.index');
});
Route::get('/opportunities/search', [OpportunityController::class, 'search'])->name('opportunities.search');
Route::get('/opportunities/{opportunity}', [OpportunityController::class, 'show'])->name('opportunities.show');
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');

// ML Matching
Route::post('/ml/match-student', [MLController::class, 'matchStudent'])->name('ml.matchStudent');
Route::middleware(['auth'])->group(function () {});


// Route for ML jobs
Route::middleware(['auth'])->group(function () {
    // Example: Route::get('/student/ml-opportunities', [StudentController::class, 'mlOpportunities'])->name('students.mlOpportunities');
    Route::get('/organization/ml-students', [OrganizationController::class, 'mlStudents'])->name('organizations.mlStudents');
});


// Admin routes (all protected by 'auth' and 'admin' middleware)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    $ctrl = \App\Http\Controllers\AdminController::class;
    Route::get('/dashboard', [$ctrl, 'dashboard'])->name('dashboard');

    // Users
    Route::get('/users', [$ctrl, 'users'])->name('users.index');
    // Organizations
    Route::get('/organizations', [$ctrl, 'organizations'])->name('organizations.index');
    Route::get('/organizations/create', [$ctrl, 'createOrganization'])->name('organizations.create');
    Route::post('/organizations', [$ctrl, 'storeOrganization'])->name('organizations.store');
    Route::get('/organizations/{id}', [$ctrl, 'showOrganization'])->name('organizations.show');
    Route::get('/organizations/{id}/edit', [$ctrl, 'editOrganization'])->name('organizations.edit');
    Route::put('/organizations/{id}', [$ctrl, 'updateOrganization'])->name('organizations.update');
    Route::delete('/organizations/{id}', [$ctrl, 'deleteOrganization'])->name('organizations.delete');

    // Students
    Route::get('/students', [$ctrl, 'students'])->name('students.index');
    Route::get('/students/{id}', [$ctrl, 'showStudent'])->name('students.show');
    Route::get('/students/{id}/edit', [$ctrl, 'editStudent'])->name('students.edit');
    Route::post('/students/{id}/edit', [$ctrl, 'updateStudent'])->name('students.update');
    Route::delete('/students/{id}', [$ctrl, 'deleteStudent'])->name('students.delete');

    // Opportunities
    Route::get('/opportunities', [$ctrl, 'opportunities'])->name('opportunities.index');
    Route::get('/opportunities/{id}', [$ctrl, 'showOpportunity'])->name('opportunities.show');
    Route::get('/opportunities/{id}/edit', [$ctrl, 'editOpportunity'])->name('opportunities.edit');
    Route::put('/opportunities/{id}/edit', [$ctrl, 'updateOpportunity'])->name('opportunities.update');
    Route::delete('/opportunities/{id}', [$ctrl, 'deleteOpportunity'])->name('opportunities.destroy');

    // Applications
    Route::get('/applications', [$ctrl, 'applications'])->name('applications.index');
    Route::get('/applications/{id}', [$ctrl, 'showApplication'])->name('applications.show');
    Route::get('/applications/{id}/edit', [$ctrl, 'editApplication'])->name('applications.edit');
    Route::post('/applications/{id}/edit', [$ctrl, 'updateApplication'])->name('applications.update');
    Route::delete('/applications/{id}', [$ctrl, 'deleteApplication'])->name('applications.delete');

    // Notifications
    Route::get('/notifications', [$ctrl, 'notifications'])->name('notifications.index');
    Route::get('/notifications/{id}', [$ctrl, 'showNotification'])->name('notifications.show');
    Route::get('/notifications/{id}/edit', [$ctrl, 'editNotification'])->name('notifications.edit');
    Route::post('/notifications/{id}/edit', [$ctrl, 'updateNotification'])->name('notifications.update');
    Route::delete('/notifications/{id}', [$ctrl, 'deleteNotification'])->name('notifications.delete');

    // Activities
    Route::get('/activities', [$ctrl, 'activities'])->name('activities.index');
    Route::get('/activities/{id}', [$ctrl, 'showActivity'])->name('activities.show');
    Route::get('/activities/{id}/edit', [$ctrl, 'editActivity'])->name('activities.edit');
    Route::post('/activities/{id}/edit', [$ctrl, 'updateActivity'])->name('activities.update');
    Route::delete('/activities/{id}', [$ctrl, 'deleteActivity'])->name('activities.delete');

    // Admin user routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
Route::post('/applications/{id}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
// Admin: Generate ML match score for application
Route::post('/admin/applications/{id}/generate-match', [ApplicationController::class, 'generateMatch'])->name('admin.applications.generate_match');


// Manual ML match calculation for student dashboard
Route::post('/student/dashboard/calculate-matches', [DashboardController::class, 'calculateMatches'])->name('student.dashboard.calculate_matches');
// Manual ML match calculation for opportunities
Route::post('/opportunities/calculate-matches', [OpportunityController::class, 'calculateMatches'])->name('opportunities.calculate_matches');
// Manual ML match calculation for student applications
Route::post('/student/applications/calculate-matches', [StudentController::class, 'calculateMatches'])->name('student.applications.calculate_matches');
// Manual ML match calculation for organization dashboard
Route::post('/organization/dashboard/calculate-matches', [DashboardController::class, 'calculateOrgMatches'])->name('organization.dashboard.calculate_matches');
// Manual ML match calculation for a student application (persist scores for that student/opportunity pair)
Route::post('/student/applications/{id}/calculate-match', [App\Http\Controllers\ApplicationController::class, 'studentCalculateMatch'])->name('student.applications.calculate_match');

require __DIR__ . '/auth.php';
