<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Opportunity Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    @include('layouts.navbar')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-3">{{ $opportunity->title }}</h1>
            <p class="text-muted">
                <i class="fas fa-building me-1"></i> {{ $opportunity->organization->name }}
                <img src="{{ $opportunity->organization->logo ? asset('storage/' . $opportunity->organization->logo) : asset('images/default-organization.png') }}" 
                             alt="{{ $opportunity->organization->name }}" 
                             class="rounded-circle" 
                             width="40" 
                             height="40">
                <span class="mx-2">•</span>
                <i class="fas fa-map-marker-alt me-1"></i> {{ $opportunity->organization->location ?? 'Remote' }}
            </p>

            <div class="mb-4">
                <span class="badge bg-info">{{ ucfirst($opportunity->type) }}</span>
            </div>

            <p>{{ $opportunity->description }}</p>

            <div class="mt-4">
                <h5>Skills Required:</h5>
                @foreach($opportunity->skills as $skill)
                    <span class="badge bg-secondary me-1">{{ $skill->name }}</span>
                @endforeach
            </div>

            <div class="mt-4 text-muted">
                <i class="fas fa-user me-1"></i> {{ $opportunity->applications_count ?? 0 }} applications received
                <br>
                Posted {{ $opportunity->created_at->diffForHumans() }}
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
