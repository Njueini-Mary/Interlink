<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Find Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    @include('layouts.navbar')
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-5 fw-bold">Find the Perfect Student</h1>
                <p class="lead text-muted">Connect with talented students for your internship and placement needs</p>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-5">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <form action="{{ route('students.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" name="keyword" placeholder="Search skills, interests or students" value="{{ request('keyword') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <select class="form-select" name="category">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <select class="form-select" name="status">
                                        <option value="">All</option>
                                        <option value="online" {{ request('status') == 'online' ? 'selected' : '' }}>Online Now</option>
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

        <!-- Popular Categories -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-4">Popular Categories</h2>
                <div class="row g-3">
                    @foreach($popularCategories as $category)
                        <div class="col-md-4 col-lg-3">
                            <a href="{{ route('students.search', ['category' => $category->id]) }}" class="text-decoration-none">
                                <div class="card h-100 shadow-sm border-0 hover-lift">
                                    <div class="card-body p-4 text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-{{ $category->icon }} fa-2x text-primary"></i>
                                        </div>
                                        <h5 class="fw-bold">{{ $category->name }}</h5>
                                        <p class="text-muted small mb-0">{{ $category->students_count }} students</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Available Freelancers -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="fw-bold mb-0">Available Students</h2>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Sort By: {{ request('sort', 'Recommended') }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'Recommended']) }}">Recommended</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'Rating']) }}">Rating</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'Newest']) }}">Newest</a></li>
                    </ul>
                </div>
            </div>
        </div>

    <!-- Student Cards -->
        <div class="row g-4">
            @forelse($students as $student)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 hover-lift">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="position-relative me-3">
                             <img src="{{ $student->profile_photo ? asset('storage/' . $student->profile_photo) : asset('images/default-avatar.png') }}" 
                                 class="rounded-circle object-fit-cover" width="70" height="70" alt="{{ $student->name }}">
                             @if($student->is_online)
                                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1" style="width:14px;height:14px;"></span>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="card-title mb-1">{{ $student->name }}</h5>
                                    <p class="text-muted mb-0">{{ $student->academic_field }}</p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-warning me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($student->rating))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-muted small">{{ number_format($student->rating, 1) }} ({{ $student->reviews_count }})</span>
                                </div>
                            </div>
                            
                            <p class="mb-3" style="min-height: 48px; max-height: 48px; overflow: hidden;">
                                {{ Str::limit($student->bio, 80) }}
                            </p>
                            
                            <div class="mb-3">
                                @foreach($student->skills->take(3) as $skill)
                                    <span class="badge bg-light text-dark me-1 mb-1">{{ $skill->name }}</span>
                                @endforeach
                                @if($student->skills->count() > 3)
                                    <span class="badge bg-light text-dark">+{{ $student->skills->count() - 3 }} more</span>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="fw-bold text-primary">{{ $student->academic_field }}</span>
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info py-4 text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h4>No students found</h4>
                        <p class="mb-0">Try adjusting your search criteria or browse our categories</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $freelancers->appends(request()->query())->links() }}
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="row mt-5">
            <div class="col-lg-10 mx-auto">
                <div class="card bg-primary text-white border-0 rounded-3 shadow">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-3">Looking for student interns?</h2>
                        <p class="lead mb-4">Post an opportunity and connect with talented students ready for internships and placements</p>
                        <a href="{{ route('opportunities.create') }}" class="btn btn-light btn-lg">Post an Opportunity</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>