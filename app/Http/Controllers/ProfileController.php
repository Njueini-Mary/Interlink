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
        $allSkills = collect(); // new collection for skills
        if ($request->user()->hasRole('freelancer')) {
            // Fetch all categories and skills from DB.
            $categories = \App\Models\Category::all();
            $allSkills = Skill::all();
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
        if ($user->hasRole('freelancer')) {
            $request->validate([
                'name'            => ['required', 'string', 'max:255'],
                'email'           => ['required', 'string', 'email', 'max:255'],
                'title'         => ['required', 'string'],
                'bio'           => ['required', 'string'],
                'hourly_rate'   => ['required', 'numeric'],
                'profile_photo' => ['nullable', 'image', 'max:2048'],
                'categories'    => ['nullable', 'array'],
                // We'll accept both an array of existing skills and an optional new_skills string.
                'existing_skills' => ['nullable', 'array'],
                'new_skills'      => ['nullable', 'string'],
            ]);

            $freelancer = $user->freelancer;
            $freelancerData = $request->only(['title', 'bio', 'hourly_rate']);
            $freelancerData['is_online'] = $request->has('is_online');

            if ($request->hasFile('profile_photo')) {
                $freelancerData['profile_photo'] = $request->file('profile_photo')->store('freelancer_photos', 'public');
            }
            $freelancer->update($freelancerData);

            // Handle categories if provided.
            if ($request->filled('categories')) {
                $freelancer->categories()->sync($request->input('categories'));
            }

            // Handle skills.
            // 1. Get existing skills selected.
            $existingSkillIds = $request->input('existing_skills', []);

            // 2. Process new skills if provided.
            $newSkillsInput = $request->input('new_skills', '');
            $newSkillIds = [];
            if (!empty($newSkillsInput)) {
                // Split by comma and trim.
                $newSkillNames = array_filter(array_map('trim', explode(',', $newSkillsInput)));
                foreach ($newSkillNames as $skillName) {
                    // Check if the skill exists.
                    $skill = Skill::firstOrCreate(['name' => $skillName]);
                    $newSkillIds[] = $skill->id;
                }
            }

            // Merge existing and new skill ids and sync.
            $allSkillIds = array_unique(array_merge($existingSkillIds, $newSkillIds));
            $freelancer->skills()->sync($allSkillIds);

        } elseif ($user->hasRole('client')) {
            // Validate client-specific fields.
            $request->validate([
                'company_name'    => ['required', 'string'],
                'company_profile' => ['required', 'string'],
                'location'        => ['required', 'string'],
                'company_logo'    => ['nullable', 'image', 'max:2048'],
            ]);

            $client = $user->client;
            $clientData = $request->only(['company_name', 'company_profile', 'location']);
            if ($request->hasFile('company_logo')) {
                $clientData['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
            }
            $client->update($clientData);
        }

        return Redirect::route('profile.update.form')->with('status', 'profile-updated');
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
