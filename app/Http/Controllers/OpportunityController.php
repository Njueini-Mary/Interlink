<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Category;
use App\Models\Skill;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OpportunityController extends Controller
{
    /**
     * Display a listing of opportunities, with optional search filters and ML matching.
     *
     * @param Request $request The HTTP request containing search parameters.
     * @return \Illuminate\View\View The view displaying the opportunities.
     */
    public function search(Request $request)
    {
        $query = Opportunity::query()->with('organization', 'category', 'skills');
        
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
        
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        
        $opportunities = $query->latest()->get();
        
        // ML match scores will be calculated manually via button
        
        $opportunities = new \Illuminate\Pagination\LengthAwarePaginator(
            $opportunities->forPage($request->input('page', 1), 10),
            $opportunities->count(),
            10,
            $request->input('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        $categories = Category::all();
        return view('opportunities.index', compact('opportunities', 'categories'));
    }
    /**
     * Show an opportunity and allow messaging the organization.
     */
    public function show($id)
    {
        // Fetch the opportunity with all related details
        $opportunity = Opportunity::with(['organization', 'category', 'skills', 'user'])
            ->findOrFail($id);
        $user = Auth::user();
        $mlData = null;
        if ($user && $user->role === 'student' && $user->student) {
            $student = $user->student;
            $application = $student->applications->where('opportunity_id', $opportunity->id)->first();
            $requiredSkills = $opportunity->skills->pluck('name')->toArray();
            $applicantSkills = $student->skills->pluck('name')->toArray();
            $matcher = app(\App\Services\SkillMatcher::class);
            if ($application) {
                $matchedSkills = is_string($application->matched_skills) ? json_decode($application->matched_skills, true) : ($application->matched_skills ?? []);
                $missingSkills = is_string($application->missing_skills) ? json_decode($application->missing_skills, true) : ($application->missing_skills ?? []);
                if (!is_array($matchedSkills) || !count($matchedSkills)) {
                    $matchedSkills = $matcher->getMatchedSkills($requiredSkills, $applicantSkills);
                }
                if (!is_array($missingSkills) || !count($missingSkills)) {
                    $missingSkills = $matcher->getMissingSkills($requiredSkills, $applicantSkills);
                }
                $mlData = [
                    'tfidfScore' => $application->tfidf_score ?? 0,
                    'logregScore' => $application->logreg_score ?? 0,
                    'matchScore' => $application->rf_score ?? $application->match_score ?? 0,
                    'requiredSkills' => $requiredSkills,
                    'applicantSkills' => $applicantSkills,
                    'matchedSkills' => $matchedSkills,
                    'missingSkills' => $missingSkills,
                    'matchMessage' => $application->match_message ?? $matcher->getMatchMessage(
                        $application->rf_score ?? $application->match_score ?? 0,
                        $matchedSkills,
                        $missingSkills,
                        $requiredSkills,
                        $student->course ?? null,
                        $student->academic_field ?? null,
                        $student->gpa ?? null,
                        $student->interests ?? null,
                        'student'
                    ),
                    'course' => $student->course ?? null,
                    'academicField' => $student->academic_field ?? null,
                    'gpa' => $student->gpa ?? null,
                    'interests' => $student->interests ?? null,
                ];
            } else {
                // No application yet, calculate on the fly
                $matchedSkills = $matcher->getMatchedSkills($requiredSkills, $applicantSkills);
                $missingSkills = $matcher->getMissingSkills($requiredSkills, $applicantSkills);
                $mlScores = $matcher->getMLScores(implode(' ', $applicantSkills), implode(' ', $requiredSkills));
                $mlData = [
                    'tfidfScore' => $mlScores['tfidf'] ?? 0,
                    'logregScore' => $mlScores['logreg'] ?? 0,
                    'matchScore' => $mlScores['rf'] ?? 0,
                    'requiredSkills' => $requiredSkills,
                    'applicantSkills' => $applicantSkills,
                    'matchedSkills' => $matchedSkills,
                    'missingSkills' => $missingSkills,
                    'matchMessage' => $matcher->getMatchMessage(
                        $mlScores['rf'] ?? 0,
                        $matchedSkills,
                        $missingSkills,
                        $requiredSkills,
                        $student->course ?? null,
                        $student->academic_field ?? null,
                        $student->gpa ?? null,
                        $student->interests ?? null,
                        'student'
                    ),
                    'course' => $student->course ?? null,
                    'academicField' => $student->academic_field ?? null,
                    'gpa' => $student->gpa ?? null,
                    'interests' => $student->interests ?? null,
                ];
            }
        }
        return view('opportunities.show', compact('opportunity', 'mlData'));
    }

    /**
     * Show the form for creating a new opportunity.
     *
     * @param Request $request The HTTP request.
     * @return \Illuminate\View\View The view for creating an opportunity.
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
        Log::info('Incoming request data', ['data' => $input]);

        $rules = [
            'currency'       => ['required', 'string', 'max:8'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'type'          => ['required', 'in:WBL,SBL'],
            'deadline'      => ['required', 'date'],
            'compensation_type' => ['required', 'in:monthly,hourly'],
            'monthly_stipend'   => ['nullable', 'numeric', 'min:0', 'required_if:compensation_type,monthly'],
            'min_rate'         => ['nullable', 'numeric', 'min:0', 'required_if:compensation_type,hourly'],
            'max_rate'         => ['nullable', 'numeric', 'min:0', 'required_if:compensation_type,hourly'],
            'category_id'   => ['nullable', 'uuid', 'exists:categories,id'],
            'new_category'  => ['nullable', 'string'],
            'existing_skills' => ['nullable', 'array'],
            'existing_skills.*' => ['uuid', 'exists:skills,id'], // Validate each skill ID
            'new_skills'      => ['nullable', 'string'],
        ];

        $validated = Validator::make($input, $rules)->validate();

        // Wrap in a transaction to ensure data integrity
        $user = Auth::user();
        $organization = $user->organizationProfile; // Use direct relationship to fetch organization profile

        if (!$organization) {
            Log::error('No valid organization profile found for user.', ['userId' => $user->id]);
            return back()->withErrors(['error' => 'No valid organization profile found. Please contact support.'])->withInput();
        }

        $opportunity = DB::transaction(function () use ($validated, $organization, $request, $user) {
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
                'min_rate'          => $validated['compensation_type'] === 'hourly' ? $validated['min_rate'] : null,
                'max_rate'          => $validated['compensation_type'] === 'hourly' ? $validated['max_rate'] : null,
                'currency'          => $validated['currency'],
                'user_id'           => $user->id, // Save the creator's user_id
            ]);

            if ($request->filled('existing_skills')) {
                Log::info('Attaching skills to opportunity', ['skills' => $request->input('existing_skills')]);
                $opportunity->skills()->attach($request->input('existing_skills'));
            }

            if ($request->filled('new_skills')) {
                $newSkills = explode(',', $request->input('new_skills'));
                foreach ($newSkills as $skillName) {
                    $skillName = trim($skillName);
                    if (!empty($skillName)) {
                        $skill = Skill::firstOrCreate(['name' => $skillName]);
                        $opportunity->skills()->attach($skill->id);
                        Log::info('Created and attached new skill', ['skill' => $skillName]);
                    }
                }
            }

            return $opportunity;
        });

        return redirect()->route('opportunities.show', $opportunity->id)
            ->with('success', 'Opportunity posted successfully.'); // Return success toaster on opportunity creation
    }

    /**
     * Display opportunities based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'organization') {
            // Show only opportunities posted by the organization
            $opportunities = Opportunity::where('organization_id', $user->organization_id)->paginate(10);
        } elseif ($user->role === 'student') {
            // Show all opportunities for students
            $opportunities = Opportunity::paginate(10);
        } else {
            abort(403, 'Unauthorized action.');
        }

        return view('opportunities.index', compact('opportunities'));
    }

    /**
     * Edit an opportunity (for organizations).
     */
    public function edit($id)
    {
        $user = Auth::user();

        // Log the incoming request details
        Log::info('Attempting to edit opportunity', ['id' => $id, 'user_id' => $user->id]);

        try {
            $opportunity = Opportunity::where('id', $id)
                ->whereHas('organization', function ($query) use ($user) {
                    $query->whereIn('id', $user->organizations->pluck('id'));
                })
                ->with('organization') // Eager load organization for debugging
                ->firstOrFail();

            Log::info('Opportunity found', [
                'id' => $id,
                'organization_ids' => $user->organizations->pluck('id'),
                'opportunity_organization_id' => $opportunity->organization->id ?? null
            ]);

            return view('opportunities.edit', compact('opportunity'));
        } catch (ModelNotFoundException $e) {
            Log::error('Opportunity not found or unauthorized access', [
                'id' => $id,
                'user_id' => $user->id,
                'organization_ids' => $user->organizations->pluck('id')
            ]);

            return abort(404, 'Opportunity not found or you do not have permission to edit it.');
        }
    }

    /**
     * Show the form for editing an opportunity.
     */
    public function editOpportunity($id)
    {
        $user = Auth::user();

        // Ensure the user is an organization and owns the opportunity
        $opportunity = Opportunity::where('id', $id)
            ->where('organization_id', $user->organization_id)
            ->firstOrFail();

        return view('opportunities.edit', compact('opportunity'));
    }

    /**
     * Update an opportunity (for organizations).
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $opportunity = Opportunity::where('id', $id)->where('organization_id', $user->organization_id)->firstOrFail();

        Log::info('Updating opportunity', ['id' => $id, 'request_data' => $request->all()]);

        $opportunity->update($request->all());

        return redirect()->route('admin.opportunities.index')->with('status', 'Opportunity updated successfully.');
    }

    /**
     * Delete an opportunity (for organizations).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $opportunity = Opportunity::where('id', $id)->where('organization_id', $user->organization_id)->firstOrFail();

        Log::info('Deleting opportunity', ['id' => $id, 'organization_id' => $user->organization_id]);

        $opportunity->delete();

        return redirect()->route('opportunities.index')->with('status', 'Opportunity deleted successfully.');
    }

    /**
     * Manually calculate ML match scores for all opportunities for the current student.
     */
    public function calculateMatches(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'student' || !$user->student) {
            return redirect()->back()->with('error', 'Only students can calculate matches.');
        }
        $matcher = new \App\Services\SkillMatcher();
        $studentSkills = $user->student->skills->pluck('name')->toArray();
        $studentProfile = $user->student->course . ' ' . ($user->student->academic_field ?? '');
        $opportunities = Opportunity::with('skills')->get();
        foreach ($opportunities as $opp) {
            $oppSkills = $opp->skills->pluck('name')->toArray();
            $oppProfile = $opp->title . ' ' . $opp->description;
            $mlScores = $matcher->getMLScores(
                implode(' ', $studentSkills) . ' ' . $studentProfile,
                implode(' ', $oppSkills) . ' ' . $oppProfile
            );
            $opp->match_score = $mlScores['rf'];
            $opp->tfidf_score = $mlScores['tfidf'];
            $opp->logreg_score = $mlScores['logreg'];
            $opp->rf_score = $mlScores['rf'];
        }
        return redirect()->back()->with('success', 'ML matches calculated!');
    }

}
