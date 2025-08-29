<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    /**
     * Display a listing of opportunities, with optional search.
     */
    public function search(Request $request)
    {
        $query = Opportunity::query()->with('organization', 'category');
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('title', 'like', "%$q%")
                  ->orWhere('description', 'like', "%$q%")
                  ->orWhereHas('organization', function($org) use ($q) {
                      $org->where('name', 'like', "%$q%");
                  });
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        $opportunities = $query->latest()->paginate(10);
        $categories = Category::all();
        return view('opportunities.index', compact('opportunities', 'categories'));
    }
    /**
     * Show an opportunity and allow messaging the organization.
     */
    public function show($id)
    {
        $opportunity = Opportunity::with('organization')->findOrFail($id);
        $organization = $opportunity->organization;
        return view('opportunities.show', compact('opportunity', 'organization'));
    }

    /**
     * Show the opportunity posting form.
     */
    public function create(Request $request)
    {
        // Fetch all categories and skills.
        $categories = Category::all();
        $allSkills = Skill::all();

        return view('opportunities.create', compact('categories', 'allSkills'));
    }

    /**
     * Store a new opportunity posting.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation rules:
        $rules = [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'type'          => ['required', 'in:WBL,SBL'],
            'deadline'      => ['required', 'date'],
            // Category is optional if new_category is provided.
            'category_id'   => ['nullable', 'exists:categories,id'],
            'new_category'  => ['nullable', 'string'],
            'existing_skills' => ['nullable', 'array'],
            'new_skills'      => ['nullable', 'string'],
        ];

        $validated = $request->validate($rules);

        // If a new category is provided and not empty, use it.
        if (!empty($request->input('new_category'))) {
            $category = Category::firstOrCreate(
                ['name' => $request->input('new_category')],
                ['description' => '']
            );
            $validated['category_id'] = $category->id;
        }

        // Create the opportunity
        $opportunity = Opportunity::create([
            'organization_id' => auth()->user()->organizationProfile->id,
            'category_id'     => $validated['category_id'],
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'type'            => $validated['type'],
            'deadline'        => $validated['deadline'],
            'status'          => 'open',
        ]);

        // Attach skills if provided
        if (!empty($request->input('existing_skills'))) {
            $opportunity->skills()->attach($request->input('existing_skills'));
        }
        // Optionally handle new skills here

        return redirect()->route('opportunities.show', $opportunity->id)
            ->with('success', 'Opportunity posted successfully.');
    }

    // ...other methods (index, show, edit, update, destroy) should be similarly refactored...
}
