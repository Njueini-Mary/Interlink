<?php

namespace App\Http\Controllers;

/**
 * OrganizationController manages organization listing, details, and related actions.
 *
 * - Lists organizations with categories and popular categories.
 * - Shows individual organization profiles.
 * - Prepares links and icons for UI.
 */

use App\Models\Organization;
use App\Models\Category;
use App\Models\Opportunity;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations.
     */
    public function index()
    {
        $organizations = Organization::paginate();
        $categories = Category::all();
        $opportunities = Opportunity::all();
        // Get top 6 categories with most opportunities
        $popularCategories = Category::withCount('opportunities')->orderByDesc('opportunities_count')->take(6)->get();
        foreach ($popularCategories as $cat) {
            if (!isset($cat->icon)) {
                $cat->icon = 'building'; // fallback FontAwesome icon
            }
        }

        foreach ($organizations as $organization) {
            $organization->profile_link = route('organizations.show', $organization->id);
        }

    return view('organizations.index', compact('categories', 'opportunities', 'organizations', 'popularCategories'));
    }

    /**
     * Display the specified organization profile.
     * 
     * Shows detailed information about an organization including:
     * - Organization profile and contact information
     * - Available opportunities
     * - Current interns/students
     * - Organization ratings and reviews
     * 
     * @param Organization $organization The organization to display (auto-resolved by route model binding)
     * @return \Illuminate\View\View View with organization details
     */
    public function show(Organization $organization)
    {
        // Load related data for the organization detail view
        $organization->load(['opportunities', 'students', 'users']);
        
        return view('organizations.show', compact('organization'));
    }

    /**
     * Show the form for creating a new organization.
     * 
     * This method is typically used by admins or during organization registration.
     * It provides the form with all necessary fields and validation rules.
     * 
     * @return \Illuminate\View\View View with organization creation form
     */
    public function create()
    {
        // Get all categories for organization categorization
        $categories = Category::all();
        
        return view('organizations.create', compact('categories'));
    }

    /**
     * Store a newly created organization in the database.
     * 
     * Validates the incoming request data and creates a new organization record.
     * Associates the organization with the authenticated user if applicable.
     * 
     * @param Request $request The HTTP request containing organization data
     * @return \Illuminate\Http\RedirectResponse Redirect to organization profile or listing
     */
    public function store(Request $request)
    {
        // Validate the incoming organization data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'location' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            // Handle logo upload if provided
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                $validated['logo'] = $logoPath;
            }

            // Create the organization record
            $organization = Organization::create($validated);

            // Associate the organization with the authenticated user if logged in
            if (auth()->check()) {
                $organization->user_id = auth()->id();
                $organization->save();
            }

            // Log the creation activity
            \Illuminate\Support\Facades\Log::info('Organization created successfully', [
                'organization_id' => $organization->id,
                'name' => $organization->name,
            ]);

            // Redirect with success message
            return redirect()->route('organizations.show', $organization->id)
                ->with('success', 'Organization created successfully!');

        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Organization creation failed: ' . $e->getMessage());
            
            // Redirect back with error message
            return back()->withInput()->withErrors(['error' => 'Failed to create organization. Please try again.']);
        }
    }

    /**
     * Show the form for editing the specified organization.
     * 
     * Ensures that only authorized users (organization owners or admins) can edit.
     * Provides pre-filled form with current organization data.
     * 
     * @param string $id The UUID of the organization to edit
     * @return \Illuminate\View\View View with organization edit form
     */
    public function edit($id)
    {
        // Retrieve the organization by ID
        $organization = Organization::findOrFail($id);
        
        // Check authorization - only organization owner or admin can edit
        $user = auth()->user();
        if ($user->role !== 'admin' && $organization->user_id !== $user->id) {
            abort(403, 'Unauthorized to edit this organization.');
        }

        // Get all categories for the form
        $categories = Category::all();

        return view('organizations.edit', compact('organization', 'categories'));
    }

    /**
     * Update the specified organization in the database.
     * 
     * Validates and updates organization information.
     * Handles logo upload if a new logo is provided.
     * 
     * @param Request $request The HTTP request containing updated organization data
     * @param string $id The UUID of the organization to update
     * @return \Illuminate\Http\RedirectResponse Redirect to organization profile
     */
    public function update(Request $request, $id)
    {
        // Retrieve the organization
        $organization = Organization::findOrFail($id);
        
        // Check authorization
        $user = auth()->user();
        if ($user->role !== 'admin' && $organization->user_id !== $user->id) {
            abort(403, 'Unauthorized to update this organization.');
        }

        // Validate the incoming data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'location' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            // Handle logo upload if a new logo is provided
            if ($request->hasFile('logo')) {
                // Delete old logo if it exists
                if ($organization->logo) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($organization->logo);
                }
                
                // Store new logo
                $logoPath = $request->file('logo')->store('logos', 'public');
                $validated['logo'] = $logoPath;
            }

            // Update the organization
            $organization->update($validated);

            // Log the update activity
            \Illuminate\Support\Facades\Log::info('Organization updated successfully', [
                'organization_id' => $organization->id,
                'name' => $organization->name,
            ]);

            // Redirect with success message
            return redirect()->route('organizations.show', $organization->id)
                ->with('success', 'Organization updated successfully!');

        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Organization update failed: ' . $e->getMessage());
            
            // Redirect back with error message
            return back()->withInput()->withErrors(['error' => 'Failed to update organization. Please try again.']);
        }
    }

    /**
     * Remove the specified organization from the database.
     * 
     * Soft deletes or permanently removes an organization record.
     * Only admins or organization owners can delete an organization.
     * 
     * @param string $id The UUID of the organization to delete
     * @return \Illuminate\Http\RedirectResponse Redirect to organizations listing
     */
    public function destroy($id)
    {
        // Retrieve the organization
        $organization = Organization::findOrFail($id);
        
        // Check authorization
        $user = auth()->user();
        if ($user->role !== 'admin' && $organization->user_id !== $user->id) {
            abort(403, 'Unauthorized to delete this organization.');
        }

        try {
            // Delete organization logo if it exists
            if ($organization->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($organization->logo);
            }

            // Delete the organization
            $organization->delete();

            // Log the deletion activity
            \Illuminate\Support\Facades\Log::info('Organization deleted successfully', [
                'organization_id' => $id,
                'name' => $organization->name,
            ]);

            // Redirect with success message
            return redirect()->route('organizations.index')
                ->with('success', 'Organization deleted successfully!');

        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Organization deletion failed: ' . $e->getMessage());
            
            // Redirect back with error message
            return back()->withErrors(['error' => 'Failed to delete organization. Please try again.']);
        }
    }

    /**
     * Get ML-matched students for an organization's opportunities.
     * 
     * Uses machine learning to find students that best match the organization's
     * open opportunities based on skills, academic field, and other factors.
     * 
     * @param Request $request The HTTP request
     * @return \Illuminate\View\View View with matched students
     */
    public function mlStudents(Request $request)
    {
        // Get the authenticated user's organization
        $user = auth()->user();
        $organization = $user->organizationProfile;

        if (!$organization) {
            abort(403, 'You must have an organization profile to access this feature.');
        }

        // Get all open opportunities for this organization
        $opportunities = $organization->opportunities()->where('status', 'open')->with('skills')->get();

        // Initialize the skill matcher service
        $matcher = new \App\Services\SkillMatcher();

        // Get all available students with their skills
        $students = \App\Models\Student::with('skills', 'user')
            ->where('is_available', true)
            ->get();

        // Array to store matched students with their scores
        $matchedStudents = [];

        // Match each student against all organization opportunities
        foreach ($students as $student) {
            $studentSkills = $student->skills->pluck('name')->toArray();
            $totalMatchScore = 0;
            $matchedOpportunities = [];

            // Check student against each opportunity
            foreach ($opportunities as $opportunity) {
                $opportunitySkills = $opportunity->skills->pluck('name')->toArray();
                
                // Calculate match percentage
                $matchPercentage = $matcher->getMatchPercentage($opportunitySkills, $studentSkills);
                
                // Get prediction
                $prediction = $matcher->predictMatch($opportunitySkills, $studentSkills);
                
                // If there's a match, add to the student's opportunities
                if ($prediction === 1) {
                    $matchedOpportunities[] = [
                        'opportunity' => $opportunity,
                        'match_percentage' => $matchPercentage,
                    ];
                    $totalMatchScore += $matchPercentage;
                }
            }

            // If student matches any opportunity, add to results
            if (count($matchedOpportunities) > 0) {
                $matchedStudents[] = [
                    'student' => $student,
                    'average_match' => round($totalMatchScore / count($matchedOpportunities), 2),
                    'matched_opportunities' => $matchedOpportunities,
                    'total_matches' => count($matchedOpportunities),
                ];
            }
        }

        // Sort students by average match score in descending order
        usort($matchedStudents, function($a, $b) {
            return $b['average_match'] <=> $a['average_match'];
        });

        // Return view with matched students
        return view('organizations.ml-students', [
            'organization' => $organization,
            'matchedStudents' => $matchedStudents,
        ]);
    }
}
