<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $student->name ?? 'Student' }} | Message Student</title>
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
                        <h1>Send a Message to {{ $student->name ?? 'Student' }} </h1>
                        <form action="{{ route('messages.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <textarea name="message" placeholder="Write your message here..." required></textarea>
                            <button type="submit">Send</button>
                        </form>

                    </div>
                    <div class="text-center mt-5">
                            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Student Listings
                            </a>
                        </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>