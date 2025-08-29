<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Student;
use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view for organizations and students.
     */
    public function create(): View
    {
        $roles = Role::whereIn('name', ['organization', 'student'])->get();
        $categories = Category::all();
        return view('auth.register', compact('roles', 'categories'));
    }

    /**
     * Handle an incoming registration request for organizations and students.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role'    => ['required', 'string', 'in:organization,student'],
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'=> ['required', 'confirmed', Rules\Password::defaults()],
            // Organization fields
            'organization_name'        => $request->role === 'organization' ? ['required', 'string'] : [],
            'organization_profile'     => $request->role === 'organization' ? ['required', 'string'] : [],
            'organization_logo'        => $request->role === 'organization' ? ['nullable', 'image', 'max:2048'] : [],
            'location'                 => $request->role === 'organization' ? ['required', 'string'] : [],
            // Student fields
            'university'   => $request->role === 'student' ? ['required', 'string'] : [],
            'course'       => $request->role === 'student' ? ['required', 'string'] : [],
            'year'         => $request->role === 'student' ? ['required', 'integer'] : [],
            'resume'       => $request->role === 'student' ? ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:4096'] : [],
        ]);

        // Create the User
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Assign role using Spatie
        $user->assignRole($request->role);

        // Create related profile
        if ($request->role === 'organization') {
            $logoPath = null;
            if ($request->hasFile('organization_logo')) {
                $logoPath = $request->file('organization_logo')->store('organization_logos', 'public');
            }
            Organization::create([
                'user_id'  => $user->id,
                'name'     => $request->organization_name,
                'profile'  => $request->organization_profile,
                'logo'     => $logoPath,
                'location' => $request->location,
            ]);
        } elseif ($request->role === 'student') {
            $resumePath = null;
            if ($request->hasFile('resume')) {
                $resumePath = $request->file('resume')->store('resumes', 'public');
            }
            Student::create([
                'user_id'    => $user->id,
                'university' => $request->university,
                'course'     => $request->course,
                'year'       => $request->year,
                'resume'     => $resumePath,
                'is_available' => true,
            ]);
        }

        // Fire registration event
        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Account created successfully! Please login.');
    }
}