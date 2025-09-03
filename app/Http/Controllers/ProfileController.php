<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the update profile information form.
     */
    public function showUpdateProfileForm(Request $request)
    {

        $categories = [];
        $allSkills = collect();
        $user = $request->user();
        if ($user->role === 'student') {
            $categories = \App\Models\Category::all();
            $allSkills = Skill::all();
        } elseif ($user->role === 'organization') {
            // Optionally fetch categories/skills for organizations if needed
        }

        return view('profile.update-profile-information-form', [
            'user'       => $request->user(),
            'categories' => $categories,
            'allSkills'  => $allSkills,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();


        // Update common user fields.
        $userData = $request->only(['name', 'email']);
        if ($request->hasFile('profile_picture')) {
            $userData['profile_picture'] = $request->file('profile_picture')->store('user_profiles', 'public');
        }
        $user->update($userData);

        // Role-specific updates.
        try {
            if ($user->role === 'student') {
                $request->validate([
                    'name'            => ['required', 'string', 'max:255'],
                    'email'           => ['required', 'string', 'email', 'max:255'],
                    'academic_field'  => ['required', 'string'],
                    'bio'             => ['required', 'string'],
                    'profile_photo'   => ['nullable', 'image', 'max:2048'],
                    'university'      => ['nullable', 'string'],
                    'course'          => ['nullable', 'string'],
                    'year'            => ['nullable', 'string'],
                    'resume'          => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                    'is_available'    => ['nullable'],
                    'categories'      => ['nullable', 'array'],
                    'existing_skills' => ['nullable', 'array'],
                    'new_skills'      => ['nullable', 'string'],
                ]);

                $student = $user->student;
                $studentData = $request->only([
                    'academic_field', 'bio', 'university', 'course', 'year'
                ]);
                $studentData['is_available'] = $request->has('is_available');

                if ($request->hasFile('profile_photo')) {
                    $studentData['profile_photo'] = $request->file('profile_photo')->store('student_photos', 'public');
                }
                if ($request->hasFile('resume')) {
                    $studentData['resume'] = $request->file('resume')->store('student_resumes', 'public');
                }
                $student->update($studentData);

                // Handle categories if provided.
                if ($request->filled('categories')) {
                    $student->categories()->sync($request->input('categories'));
                }

                // Handle skills.
                $existingSkillIds = $request->input('existing_skills', []);
                $newSkillsInput = $request->input('new_skills', '');
                $newSkillIds = [];
                if (!empty($newSkillsInput)) {
                    $newSkillNames = array_filter(array_map('trim', explode(',', $newSkillsInput)));
                    foreach ($newSkillNames as $skillName) {
                        $skill = Skill::firstOrCreate(['name' => $skillName]);
                        $newSkillIds[] = $skill->id;
                    }
                }
                $allSkillIds = array_unique(array_merge($existingSkillIds, $newSkillIds));
                $student->skills()->sync($allSkillIds);

            } elseif ($user->role === 'organization') {
                $request->validate([
                    'organization_name'    => ['required', 'string'],
                    'organization_profile' => ['required', 'string'],
                    'location'             => ['required', 'string'],
                    'company_logo'         => ['nullable', 'image', 'max:2048'],
                ]);

                $organization = $user->organizationProfile;
                $orgData = [
                    'name'        => $request->input('organization_name'),
                    'description' => $request->input('organization_profile'),
                    'location'    => $request->input('location'),
                ];
                if ($request->hasFile('company_logo')) {
                    $orgData['logo'] = $request->file('company_logo')->store('organization_logos', 'public');
                }
                $organization->update($orgData);
            }

            // You can add admin or other role logic here if needed

            return Redirect::route('profile.update.form')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return Redirect::route('profile.update.form')->with('error', 'Profile update failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        \Illuminate\Support\Facades\Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
