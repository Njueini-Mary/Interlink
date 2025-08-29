<!-- resources/views/student.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Available Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    @include('components.navbar')

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="fw-bold">Available Students</h1>
                <p class="text-muted">Browse our pool of students seeking internships and placements</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('students.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Search by name or skill" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="course" class="form-label">Course</label>
                                <select class="form-select" id="course" name="course">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="seeking" {{ request('status') == 'seeking' ? 'selected' : '' }}>Seeking Placement</option>
                                    <option value="placed" {{ request('status') == 'placed' ? 'selected' : '' }}>Placed</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @forelse($students as $student)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="position-relative">
                                    <img src="{{ $student->profile_photo ? asset('storage/' . $student->profile_photo) : asset('images/default-avatar.png') }}" alt="{{ $student->name }}" class="rounded-circle" width="60" height="60">
                                    <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $student->status == 'seeking' ? 'warning' : 'success' }} rounded-circle" style="width: 12px; height: 12px;"></span>
                                </div>
                                <div class="ms-3">
                                    <h5 class="card-title mb-0">{{ $student->name }}</h5>
                                    <p class="text-muted mb-0">{{ $student->course->name ?? 'Student' }}</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Skills:</small>
                                <div class="mt-1">
                                    @foreach($student->skills as $skill)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $skill->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">University:</small>
                                <div class="mt-1">{{ $student->university->name ?? 'N/A' }}</div>
                            </div>
                            <p class="card-text" style="height: 48px; overflow: hidden;">{{ Str::limit($student->bio, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Year: {{ $student->year_of_study ?? 'N/A' }}</span>
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <p class="mb-0">No students found matching your criteria.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </div>

    @include('components.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>