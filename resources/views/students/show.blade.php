<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $freelancer->name }} | Freelancer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    @include('layouts.navbar')

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-4 position-relative">
                                <img src="{{ $freelancer->profile_photo ? asset('storage/' . $freelancer->profile_photo) : asset('images/default-avatar.png') }}"
                                     class="rounded-circle object-fit-cover" width="120" height="120" alt="{{ $freelancer->name }}">
                                @if($freelancer->is_online)
                                    <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2" style="width:18px;height:18px;"></span>
                                @endif
                            </div>
                            <div>
                                <h2 class="fw-bold mb-1">{{ $freelancer->name }}</h2>
                                <p class="text-muted mb-2">{{ $freelancer->title }}</p>
                                <div class="d-flex align-items-center">
                                    <div class="text-warning me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($freelancer->rating))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-muted">{{ number_format($freelancer->rating, 1) }} ({{ $freelancer->reviews_count }} reviews)</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold">Bio</h5>
                            <p class="text-muted">{{ $freelancer->bio }}</p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold">Skills</h5>
                            @foreach($freelancer->skills as $skill)
                                <span class="badge bg-light text-dark me-2 mb-2">{{ $skill->name }}</span>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold">Hourly Rate</h5>
                            <p class="text-primary fw-bold fs-5">Kshs{{ number_format($freelancer->hourly_rate, 2) }}/hr</p>
                        </div>

                        @if($freelancer->projects->count())
                            <div class="mb-4">
                                <h5 class="fw-bold">Portfolio</h5>
                                <div class="row g-3">
                                    @foreach($freelancer->projects as $project)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card shadow-sm border-0 h-100">
                                                <img src="{{ $project->thumbnail ? asset('storage/' . $project->thumbnail) : asset('images/placeholder.png') }}"
                                                     class="card-img-top" alt="{{ $project->title }}">
                                                <div class="card-body">
                                                    <h6 class="fw-bold">{{ $project->title }}</h6>
                                                    <p class="text-muted small">{{ Str::limit($project->description, 80) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-5">
                            <a href="{{ route('freelancers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Listings
                            </a>
                            <a href="{{ route('messages.create', ['freelancer_id' => $freelancer->id]) }}" class="btn btn-primary ms-2">
                                <i class="fas fa-envelope me-1"></i> Contact {{ $freelancer->name }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
