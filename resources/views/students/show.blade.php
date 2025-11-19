<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $student->name }} | Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    @include('layouts.navbar')

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-4 bg-primary text-white d-flex flex-column align-items-center justify-content-center p-4">
                            <div class="position-relative mb-3">
                                <img src="{{ $student->profile_photo ? asset('storage/' . $student->profile_photo) : asset('images/default-avatar.png') }}"
                                     class="rounded-circle border border-4 border-white shadow object-fit-cover" width="140" height="140" alt="{{ $student->name }}">
                                @if($student->is_online)
                                    <span class="position-absolute bottom-0 end-0 translate-middle p-2 bg-success border border-light rounded-circle" style="width:20px;height:20px;"></span>
                                @endif
                            </div>
                            <h2 class="fw-bold mb-1">{{ $student->name }}</h2>
                            <p class="mb-2"><i class="fas fa-graduation-cap me-1"></i> {{ $student->academic_field }}</p>
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
                                <span>{{ number_format($student->rating, 1) }} <small>({{ $student->reviews_count }} reviews)</small></span>
                            </div>
                            <div class="mb-2">
                                @foreach($student->skills as $skill)
                                    <span class="badge bg-light text-primary border border-primary me-1 mb-1">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('messages.create', ['student_id' => $student->id]) }}" class="btn btn-light btn-sm shadow-sm">
                                    <i class="fas fa-envelope me-1"></i> Contact
                                </a>
                            </div>
                        </div>
                        <div class="col-md-8 p-5">
                            <div class="mb-4">
                                <h4 class="fw-bold mb-3"><i class="fas fa-user"></i> Bio</h4>
                                <p class="text-muted">{{ $student->bio }}</p>
                            </div>
                            <div class="mb-4">
                                <h4 class="fw-bold mb-3"><i class="fas fa-university"></i> Academic Details</h4>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="bg-light rounded-3 p-3 mb-2">
                                            <strong>University:</strong> {{ $student->university ?? '-' }}
                                        </div>
                                        <div class="bg-light rounded-3 p-3 mb-2">
                                            <strong>Course:</strong> {{ $student->course ?? '-' }}
                                        </div>
                                        <div class="bg-light rounded-3 p-3 mb-2">
                                            <strong>Year:</strong> {{ $student->year ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="bg-light rounded-3 p-3 mb-2">
                                            <strong>Academic Field:</strong> {{ $student->academic_field ?? '-' }}
                                        </div>
                                        <div class="bg-light rounded-3 p-3 mb-2">
                                            <strong>Available:</strong> <span class="badge {{ $student->is_available ? 'bg-success' : 'bg-secondary' }}">{{ $student->is_available ? 'Yes' : 'No' }}</span>
                                        </div>
                                        <div class="bg-light rounded-3 p-3 mb-2">
                                            <strong>Resume:</strong>
                                            @if($student->resume)
                                                <a href="{{ asset('storage/' . $student->resume) }}" target="_blank" class="btn btn-outline-primary btn-sm ms-2"><i class="fas fa-file-pdf"></i> View Resume</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-5">
                                <a href="{{ route('students.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Listings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-extrabold mb-4">Applications</h2>

                    @if($applications->count())
                        <div class="row g-4">
                            @foreach($applications as $application)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="badge bg-success bg-opacity-10 text-success me-2 p-2"><i class="fa-solid fa-arrow-up-from-bracket fa-lg"></i></span>
                                                <h5 class="mb-0 fw-semibold text-dark">{{ $application->opportunity->title ?? 'N/A' }}</h5>
                                            </div>
                                            <p class="text-muted mb-1 small">Submitted: <span class="fw-medium">{{ $application->created_at->format('M d, Y') }}</span></p>
                                            <span class="badge rounded-pill bg-{{ $application->status === 'accepted' ? 'success' : ($application->status === 'pending' ? 'warning text-dark' : 'secondary') }} mb-3">{{ ucfirst($application->status) }}</span>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <a href="{{ route('opportunities.show', $application->opportunity_id) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-eye me-1"></i> View</a>
                                                <span class="text-muted small"><i class="fa-solid fa-building me-1"></i>{{ $application->organization->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $applications->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-circle-info fa-lg me-2"></i>
                            <div>No applications found for this student.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
