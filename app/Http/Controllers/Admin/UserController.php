<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * This method retrieves a paginated list of users from the database.
     * It supports filtering by role (admin, student, organization) and
     * searching by name or email.
     *
     * @param Request $request The HTTP request instance containing query parameters.
     * @return \Illuminate\View\View The view displaying the list of users.
     */
    public function index(Request $request)
    {
        $users = User::query();

        // Filter users by role if provided in the request
        if ($request->has('role')) {
            $validRoles = ['admin', 'student', 'organization'];
            $role = $request->input('role');

            if (in_array($role, $validRoles)) {
                $users->where('role', $role);
            }
        }

        // Search users by name or email if provided in the request
        if ($request->has('search')) {
            $users->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('email', 'like', '%' . $request->input('search') . '%');
            });
        }

        // Return the view with paginated users
        return view('admin.users.index', [
            'users' => $users->paginate(10),
        ]);
    }

    /**
     * Display the specified user.
     *
     * This method retrieves and displays the details of a specific user.
     *
     * @param User $user The user instance to display.
     * @return \Illuminate\View\View The view displaying the user details.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * This method displays a form to edit the details of a specific user.
     *
     * @param User $user The user instance to edit.
     * @return \Illuminate\View\View The view displaying the edit form.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * This method validates and updates the details of a specific user in the database.
     *
     * @param Request $request The HTTP request instance containing user input.
     * @param User $user The user instance to update.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the user index page.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|string|in:admin,student,organization',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * This method deletes a specific user from the database.
     *
     * @param User $user The user instance to delete.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the user index page.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to delete user.');
        }
    }
}