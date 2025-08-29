<?php

namespace App\Http\Controllers;

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

        foreach ($organizations as $organization) {
            $organization->profile_link = route('organizations.show', $organization->id);
        }

        return view('organizations.index', compact('categories', 'opportunities', 'organizations'));
    }

    public function show(Organization $organization)
    {
        return view('organizations.show', compact('organization'));
    }

    // ...other methods (create, store, edit, update, etc.) should be similarly refactored...
}
