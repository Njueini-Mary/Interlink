<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Find Organizations & Opportunities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
    @include('layouts.navbar')

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-5 fw-bold">Find Opportunities & Organizations</h1>
                <p class="lead text-muted">Discover internships and placements that match your skills and interests</p>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-5">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <form action="#" method="GET">
                            <div class="row g-3">
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" name="keyword"
                                            placeholder="Search opportunities, skills or organizations"
                                            value="{{ request('keyword') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <select class="form-select" name="category">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <select class="form-select" name="budget">
                                        <option value="">Any Budget</option>
                                        <option value="hourly" {{ request('budget') == 'hourly' ? 'selected' : '' }}>
                                            Hourly</option>
                                        <option value="fixed" {{ request('budget') == 'fixed' ? 'selected' : '' }}>Fixed
                                            Price</option>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <button type="submit" class="btn btn-primary w-100">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Type Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-tabs" id="opportunityTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-opportunities"
                            type="button" role="tab" aria-controls="all-opportunities" aria-selected="true">All Opportunities</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="organizations-tab" data-bs-toggle="tab" data-bs-target="#organization-profiles"
                            type="button" role="tab" aria-controls="organization-profiles" aria-selected="false">Organization
                            Profiles</button>
                    </li>
                </ul>
            </div>
        </div>

    <div class="tab-content" id="opportunityTabsContent">
            <!-- All Jobs Tab -->
            <div class="tab-pane fade show active" id="all-opportunities" role="tabpanel" aria-labelledby="all-tab">
                <!-- Popular Job Categories -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h2 class="fw-bold mb-4">Popular Opportunity Categories</h2>
                        <div class="row g-3">
                            @foreach($popularCategories as $category)
                            <div class="col-md-4 col-lg-3">
                                <a href="{{ route('opportunities.search', ['category' => $category->id]) }}"
                                    class="text-decoration-none">
                                    <div class="card h-100 shadow-sm border-0 hover-lift">
                                        <div class="card-body p-4 text-center">
                                            <div class="mb-3">
                                </a> <i class="fas fa-{{ $category->icon }} fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">{{ $category->name }}</h5>
                            <p class="text-muted small mb-0">{{ $category->opportunities_count }} active opportunities</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Available Jobs -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Available Opportunities</h2>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Sort By: {{ request('sort', 'Most Recent') }}
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item"
                            href="{{ request()->fullUrlWithQuery(['sort' => 'Most Recent']) }}">Most Recent</a></li>
                    <li><a class="dropdown-item"
                            href="{{ request()->fullUrlWithQuery(['sort' => 'Highest Budget']) }}">Highest Budget</a>
                    </li>
                    <li><a class="dropdown-item"
                            href="{{ request()->fullUrlWithQuery(['sort' => 'Most Proposals']) }}">Most Proposals</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Jobs List -->
    <div class="row g-4">
    @forelse($opportunities as $opportunity)
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="badge bg-info mb-2">Internship</span>
                        <span class="text-muted small">{{ $opportunity->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">{{ $opportunity->title }}</h5>
                        <img src="{{ $opportunity->organization->logo ? asset('storage/' . $opportunity->organization->logo) : asset('images/default-organization.jpg') }}" 
                            alt="{{ $opportunity->organization->name }}" 
                            class="rounded-circle" 
                            width="40" 
                            height="40">
                    </div>
                    <p class="text-muted mb-3 small">
                        <i class="fas fa-building me-1"></i> {{ $opportunity->organization->name }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-map-marker-alt me-1"></i> {{ $opportunity->organization->location ?? 'Remote' }}
                    </p>

                    <p class="mb-3" style="min-height: 48px; max-height: 48px; overflow: hidden;">
                        {{ Str::limit($opportunity->description, 100) }}
                    </p>

                    <div class="mb-3">
                        @foreach($opportunity->skills->take(3) as $skill)
                        <span class="badge bg-light text-dark me-1 mb-1">{{ $skill->name }}</span>
                        @endforeach
                        @if($opportunity->skills->count() > 3)
                        <span class="badge bg-light text-dark">+{{ $opportunity->skills->count() - 3 }} more</span>
                        @endif
                    </div>

                    @auth
                        @if(auth()->user()->role === 'student' && auth()->user()->student)
                            @php
                                $student = auth()->user()->student;
                                $studentSkills = $student->skills->pluck('name')->toArray();
                                $opportunitySkills = $opportunity->skills->pluck('name')->toArray();
                                $matcher = app(\App\Services\SkillMatcher::class);
                                $mlScores = $matcher->getMLScores(
                                    implode(' ', $studentSkills) . ' ' . ($student->bio ?? ''),
                                    implode(' ', $opportunitySkills) . ' ' . ($opportunity->description ?? '')
                                );
                                $matchedSkills = $matcher->getMatchedSkills($opportunitySkills, $studentSkills);
                                $missingSkills = $matcher->getMissingSkills($opportunitySkills, $studentSkills);
                                $xai = $matcher->getMatchMessage(
                                    $mlScores['tfidf'] ?? 0,
                                    $matchedSkills,
                                    $missingSkills,
                                    $opportunitySkills,
                                    $student->course ?? null,
                                    $student->academic_field ?? null,
                                    $student->gpa ?? null,
                                    $student->interests ? (is_array($student->interests) ? $student->interests : explode(',', $student->interests)) : null,
                                    $student->year ?? null,
                                    $student->university ?? null,
                                    null,
                                    [
                                        'skills' => ['matched' => $matchedSkills, 'missing' => $missingSkills],
                                        'course' => ['matched' => [$student->course], 'missing' => []],
                                        'academic_field' => ['matched' => [$student->academic_field], 'missing' => []],
                                        'gpa' => ['matched' => [$student->gpa], 'missing' => []],
                                        'interests' => ['matched' => is_array($student->interests) ? $student->interests : explode(',', $student->interests), 'missing' => []],
                                    ]
                                );
                            @endphp
                            <div class="mb-2">
                                <span class="fw-bold text-primary">Your ML Match: {{ round($mlScores['tfidf'] ?? 0) }}%</span>
                                <span class="text-muted small ms-2">(TFIDF)</span>
                            </div>
                            <div class="mb-2">
                                <span class="fw-bold text-gray-700">Why this score?</span>
                                <div class="text-sm text-indigo-700 bg-indigo-50 rounded p-2 mt-1">{{ $xai }}</div>
                            </div>
                        @endif
                    @endauth

                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <div>
                            <span class="fw-bold text-primary">
                                {{ $opportunity->type ?? 'Internship' }}
                            </span>
                            <span class="text-muted small ms-2">
                                <i class="fas fa-user me-1"></i> {{ $opportunity->applications_count ?? 0 }} applications
                            </span>
                        </div>
                        <a href="{{ route('opportunities.show', $opportunity->id) }}" class="btn btn-sm btn-outline-primary">View
                            Details</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info py-4 text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h4>No opportunities found</h4>
                <p class="mb-0">Try adjusting your search criteria or browse our categories</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->

    </div>

    <!-- Client Profiles Tab -->
    <div class="tab-pane fade" id="organization-profiles" role="tabpanel" aria-labelledby="organizations-tab">
        <!-- Client Filters -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="fw-bold mb-0">Organizations</h2>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="clientSortDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Sort By: {{ request('client_sort', 'Most Active') }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="clientSortDropdown">
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['client_sort' => 'Most Active']) }}">Most
                                Active</a></li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['client_sort' => 'Highest Rating']) }}">Highest
                                Rating</a></li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['client_sort' => 'Newest']) }}">Newest</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Client Cards -->
        <div class="row g-4">
            @forelse($organizations as $organization)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="position-relative me-3">
                                <img src="{{ $organization->logo ? asset('storage/' . $organization->logo) : asset('images/default-organization.jpg') }}"
                                    class="rounded-circle object-fit-cover" width="70" height="70"
                                    alt="{{ $organization->name }}">
                                @if($organization->is_verified)
                                <span class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-1"
                                    style="width:14px;height:14px;"><i class="fas fa-check fa-xs text-white"></i></span>
                                @endif
                            </div>
                            <div>
                                <h5 class="card-title mb-1">{{ $organization->name }}</h5>
                                <p class="text-muted mb-0">{{ $organization->industry }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-warning me-2">
                                    @for($i = 1; $i <= 5; $i++) @if($i <=round($organization->rating))
                                        <i class="fas fa-star"></i>
                                        @else
                                        <i class="far fa-star"></i>
                                        @endif
                                        @endfor
                                </div>
                                <span class="text-muted small">{{ number_format($organization->rating, 1) }}
                                    ({{ $organization->reviews_count }})</span>
                            </div>
                        </div>

                        <p class="mb-3" style="min-height: 48px; max-height: 48px; overflow: hidden;">
                            {{ Str::limit($organization->description, 80) }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>
                                {{ $organization->location ?? 'Remote' }}</span>
                            <span class="text-muted small"><i class="fas fa-briefcase me-1"></i>
                                {{ $organization->active_opportunities_count ?? 0 }} active opportunities</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="text-muted small">
                                <i class="fas fa-shield-alt me-1 text-success"></i>
                                {{ $organization->verified ? 'Verified' : 'Unverified' }}
                            </span>
                            <a href="{{ route('organizations.show', $organization->id) }}"
                                class="btn btn-sm btn-outline-primary">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info py-4 text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>No organizations found</h4>
                    <p class="mb-0">Try adjusting your search criteria</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $organizations->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    </div>

    <!-- Call to Action -->
    @guest

    <div class="row mt-5">
        <div class="col-lg-10 mx-auto">
            <div class="card bg-primary text-white border-0 rounded-3 shadow">
                <div class="card-body p-5 text-center">
                    <h2 class="fw-bold mb-3">Are you a student?</h2>
                    <p class="lead mb-4">Join our platform and connect with organizations looking for your skills</p>
                    <a href="/register" class="btn btn-light btn-lg">Register as Student</a>
                </div>
            </div>
        </div>
    </div>
    @endguest

    </div>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>