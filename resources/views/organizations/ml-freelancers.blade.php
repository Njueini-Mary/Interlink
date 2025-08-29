<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Find Freelancers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
    @include('layouts.navbar')
    <div class="container">
    <h2>ML-Powered Freelancer Matches</h2>
    
    <div class="row">
        @foreach($recommendedFreelancers as $freelancer)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            {{ $freelancer->user->name }}
                            <span class="badge bg-success">
                                {{ number_format($freelancer->match_percentage, 1) }}% Match
                            </span>
                        </h5>
                        
                        <div class="skills mb-2">
                            @foreach($freelancer->skills as $skill)
                                <span class="badge bg-primary">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                        
                        <p class="card-text">{{ Str::limit($freelancer->bio, 100) }}</p>
                        
                        <div class="mt-2">
                            <span class="text-muted">
                                ML Model Version: {{ filemtime(storage_path('app/ml/skill_model.dat')) }}
                            </span>
                        </div>

                        <a href="{{ route('freelancers.show', $freelancer->id) }}" class="btn btn-primary">
                            View Profile
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>