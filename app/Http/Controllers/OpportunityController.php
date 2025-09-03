<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
    $input = $request->all();
    Log::info('Incoming request category_id', ['category_id' => $request->input('category_id'), 'input' => $input]);


        // If a new category is provided, create it and set category_id to its UUID
        if (!empty($input['new_category'])) {
            $category = Category::firstOrCreate(
                ['name' => $input['new_category']],
                ['description' => '']
            );
            $input['category_id'] = $category->id;
        }

        // If category_id is empty or not a valid UUID, set to null
        // Accept UUIDs with dashes and lowercase
        if (empty($input['category_id']) || !preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $input['category_id'])) {
            $input['category_id'] = null;
        }

        // Require at least one valid category
        if (empty($input['category_id'])) {
            return back()->withErrors(['category_id' => 'Please select a category or create a new one.'])->withInput();
        }

        $rules = [
            'currency'       => ['required', 'string', 'max:8'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'type'          => ['required', 'in:WBL,SBL'],
            'deadline'      => ['required', 'date'],
            'compensation_type' => ['required', 'in:monthly,hourly'],
            'monthly_stipend'   => ['nullable', 'numeric', 'min:0', 'required_if:compensation_type,monthly'],
            'min_hourly_rate'   => ['nullable', 'numeric', 'min:0', 'required_if:compensation_type,hourly'],
            'max_hourly_rate'   => ['nullable', 'numeric', 'min:0', 'required_if:compensation_type,hourly'],
            'category_id'   => ['nullable', 'uuid', 'exists:categories,id'],
            'new_category'  => ['nullable', 'string'],
            'existing_skills' => ['nullable', 'array'],
            'new_skills'      => ['nullable', 'string'],
        ];

        $validated = Validator::make($input, $rules)->validate();

        // Get the organization for the current user (by user_id UUID)
        $user = \Illuminate\Support\Facades\Auth::user();
        Log::info('OpportunityController@store: user_id', ['user_id' => $user->id]);
        $organization = \App\Models\Organization::where('user_id', $user->id)->first();
        Log::info('OpportunityController@store: organization', ['organization' => $organization]);
        if (!$organization || !preg_match('/^[0-9a-fA-F-]{36}$/', $organization->id)) {
            Log::error('No valid organization found for user', ['user_id' => $user->id]);
            return back()->withErrors(['organization_id' => 'No valid organization profile found for your account. Please contact support.'])->withInput();
        }
        // Create the opportunity
        try {
            $opportunity = Opportunity::create([
                'organization_id'   => $organization->id,
                'category_id'       => $validated['category_id'],
                'title'             => $validated['title'],
                'description'       => $validated['description'],
                'type'              => $validated['type'],
                'deadline'          => $validated['deadline'],
                'status'            => 'open',
                'compensation_type' => $validated['compensation_type'],
                'monthly_stipend'   => $validated['compensation_type'] === 'monthly' ? $validated['monthly_stipend'] : null,
                'min_hourly_rate'   => $validated['compensation_type'] === 'hourly' ? $validated['min_hourly_rate'] : null,
                'max_hourly_rate'   => $validated['compensation_type'] === 'hourly' ? $validated['max_hourly_rate'] : null,
                'currency'          => $validated['currency'],
            ]);
            Log::info('Opportunity created', ['opportunity' => $opportunity, 'id' => $opportunity->id]);
            if (!$opportunity || empty($opportunity->id) || $opportunity->id === 0) {
                Log::error('Opportunity creation failed or ID is 0', ['opportunity' => $opportunity]);
                return back()->withErrors(['opportunity' => 'Failed to create opportunity. Please try again or contact support.'])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Opportunity creation exception', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['opportunity' => 'Database error: ' . $e->getMessage()])->withInput();
        }

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
