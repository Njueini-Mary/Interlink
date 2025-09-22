<?php
namespace App\Http\Controllers\Auth;
// RegisteredUserController handles registration for organizations and students.
// - Uses the 'role' field in User model for authorization.
// - Creates related Organization or Student profile with all required fields.
// - Enforces email verification after registration.
// - Modern registration view supports all profile fields and file uploads.

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
use App\Providers\RouteServiceProvider;



class RegisteredUserController extends Controller
{
    /**
     * Display the registration view for organizations and students.
     *
     * @return View
     */
    public function create(): View
    {
        // NOTE: Role model is not used anymore, but if you still need to show role options, you can hardcode them or fetch from config.
        $roles = [
            (object)['name' => 'organization'],
            (object)['name' => 'student'],
        ];
        $categories = Category::all();
        return view('auth.register', compact('roles', 'categories'));
    }

    /**
     * Handle an incoming registration request for organizations and students.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Log incoming registration request data for debugging
    Log::info('Registration request received', $request->except(['password', 'password_confirmation']));

        // Validate required fields for organization and student registration
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
    Log::info('Registration validation passed', ['role' => $request->role, 'email' => $request->email, 'contact_email' => $request->contact_email]);

        // Use a database transaction to ensure atomicity:
        // If any error occurs during user/profile creation, all changes are rolled back and no partial data is saved.
        try {
            DB::beginTransaction();
            Log::info('Registration transaction started');

            // Step 1: Create the User record (organization uses its own email for login)
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);
            Log::info('User created', ['user_id' => $user->id, 'email' => $user->email]);

            // Step 2: Create the organization profile
            if ($request->role === 'organization') {
                $logoPath = null;
                if ($request->hasFile('organization_logo')) {
                    $logoPath = $request->file('organization_logo')->store('organization_logos', 'public');
                }
                Log::info('Organization logo path', ['logoPath' => $logoPath]);
                $organization = Organization::create([
                    'name'         => $request->organization_name,
                    'profile'      => $request->organization_profile,
                    'logo'         => $logoPath,
                    'location'     => $request->location,
                    'industry'     => $request->industry,
                    'website'      => $request->website,
                    'contact_email'=> $request->email,
                    'contact_phone'=> $request->contact_phone,
                    'user_id'      => $user->id, // Associate the user_id with the organization
                ]);
                Log::info('Organization created', ['organization_id' => $organization->id, 'user_id' => $user->id]);
            } elseif ($request->role === 'student') {
                // Handle student resume upload
                $resumePath = null;
                if ($request->hasFile('resume')) {
                    // Store resume in public/resumes
                    $resumePath = $request->file('resume')->store('resumes', 'public');
                }
                Log::info('Student resume path', ['resumePath' => $resumePath]);
                // Create student profile with all required fields
                Student::create([
                    'user_id'      => $user->id,
                    'university'   => $request->university,
                    'course'       => $request->course,
                    'year'         => $request->year,
                    'resume'       => $resumePath,
                    'academic_field'=> $request->academic_field,
                    'bio'          => $request->bio,
                    'is_available' => true,
                ]);
                Log::info('Student profile created', ['user_id' => $user->id]);
            }

            // Step 3: Commit the transaction if everything succeeded
            DB::commit();
            Log::info('Registration transaction committed');
        } catch (\Exception $e) {
            // If any error occurs, rollback all changes and return with error message
            DB::rollBack();
            Log::error('Registration error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            // Return error as toast notification
            return back()->withInput()->with('status', 'Registration failed: ' . $e->getMessage());
        }

        // Step 4: Redirect to login with success message for toast
    // Return success as toast notification
    return redirect()->route('login')->with('status', 'Registration successful! Please login.');
    }
}