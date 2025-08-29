<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post a New Opportunity | Interlink Placement System</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Optional FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom styles -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
  @include('layouts.navbar')
<div class="container py-5">
    <h1 class="mb-4 text-center">My Opportunity Listings</h1>

    <!-- Filter by Category -->
    <form method="GET" action="{{ route('opportunities.search') }}" class="row g-3 mb-4">
        <div class="col-md-6">
            <select name="category" class="form-select">
                <option value="">-- Filter by Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </form>

    <!-- Opportunity Listings -->
    @if ($opportunities->count())
        <div class="list-group">
            @foreach ($opportunities as $opportunity)
                <div class="list-group-item">
                    <h5 class="mb-1">{{ $opportunity->title }}</h5>
                    <p class="mb-1">{{ \Illuminate\Support\Str::limit($opportunity->description, 150) }}</p>
                    <div class="small text-muted">
                        Type: <strong>{{ ucfirst($opportunity->type) }}</strong> |
                        Category: <strong>{{ $opportunity->category->name ?? 'N/A' }}</strong> |
                        Deadline: <strong>{{ \Carbon\Carbon::parse($opportunity->deadline)->toFormattedDateString() }}</strong>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $opportunities->withQueryString()->links() }}
        </div>
    @else
    <p class="text-muted text-center">No opportunities found.</p>
    @endif
</div>
  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>