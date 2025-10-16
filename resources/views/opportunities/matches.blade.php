<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Matched Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
    @include('layouts.navbar')
    <div class="container">
    <h1>Matched Students for Opportunity: {{ $opportunity->title }}</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Skills</th>
                    <th>Experience Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($matches as $student)
                <tr>
                    <td>{{ $student->user->name }}</td>
                    <td>{{ $student->skills->pluck('name')->join(', ') }}</td>
                    <td>{{ $student->academic_level ?? $student->experience_level }}</td>
                    @if(isset($student->match_score))
                    <td>
                        <strong>TF-IDF:</strong> {{ round($student->tfidf_score ?? 0) }}%<br>
                        <strong>LogReg:</strong> {{ round($student->logreg_score ?? 0) }}%<br>
                        <strong>RF:</strong> {{ round($student->rf_score ?? 0) }}%
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>