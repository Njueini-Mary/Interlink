<?php
/**
 * AuthenticatedSessionController handles login, logout, and session management for users.
 *
 * - Uses the 'role' field in User model for dashboard redirection.
 * - Logs authentication events for auditing.
 * - Supports admin, organization, and student dashboards.
 */
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
    Log::info('Authenticated user after login', [
            'id' => $user->id ?? null,
            'name' => $user->name ?? null,
            'email' => $user->email ?? null,
            'role' => $user->role ?? null,
        ]);

        // Set a toast message for all user types
        $toast = ['status' => 'Login successful! Welcome back, ' . $user->name . '.'];

        // Redirect user to their dashboard based on role
        // For organization staff, each user has their own account and dashboard
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'))->with($toast);
        } elseif ($user->role === 'organization') {
            return redirect()->intended(route('organization.dashboard'))->with($toast);
        } elseif ($user->role === 'student') {
            return redirect()->intended(route('student.dashboard'))->with($toast);
        }
        // Default fallback
        return redirect()->intended(route('dashboard'))->with($toast);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
