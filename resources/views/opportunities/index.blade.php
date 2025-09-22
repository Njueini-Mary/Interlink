<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Opportunities | Interlink Placement System</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  @include('layouts.navbar')

  <div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold text-center mb-6">
      {{ auth()->user()->role === 'organization' ? 'My Opportunity Listings' : 'All Opportunities' }}
    </h1>

    <!-- Filter by Category -->
    <form method="GET" action="{{ route('opportunities.search') }}" class="flex justify-center mb-6">
      <select name="category" class="border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
        <option value="">-- Filter by Category --</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
            {{ $category->name }}
          </option>
        @endforeach
      </select>
      <button type="submit" class="ml-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600">Apply Filter</button>
    </form>

    <!-- Opportunity Listings -->
    @if ($opportunities->count())
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($opportunities as $opportunity)
          <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-2">{{ $opportunity->title }}</h2>
            <p class="text-gray-700 mb-4">{{ \Illuminate\Support\Str::limit($opportunity->description, 150) }}</p>
            <div class="text-sm text-gray-500 mb-4">
              Type: <strong>{{ ucfirst($opportunity->type) }}</strong> |
              Category: <strong>{{ $opportunity->category->name ?? 'N/A' }}</strong> |
              Deadline: <strong>{{ \Carbon\Carbon::parse($opportunity->deadline)->toFormattedDateString() }}</strong>
            </div>
            <div class="flex justify-between">
              @if (auth()->user()->role === 'organization' && auth()->user()->id === $opportunity->organization_id)
                <a href="{{ route('admin.opportunities.edit', $opportunity->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow hover:bg-yellow-600">Edit</a>
                <form action="{{ route('admin.opportunities.destroy', $opportunity->id) }}" method="POST" class="inline">
                  @csrf
                  @method('DELETE')
                </form>
              @elseif (auth()->user()->role === 'student')
                <a href="{{ route('opportunities.show', $opportunity->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600">View & Apply</a>
              @else
                <a href="{{ route('opportunities.show', $opportunity->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600">View</a>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        {{ $opportunities->withQueryString()->links() }}
      </div>
    @else
      <p class="text-center text-gray-500">No opportunities found.</p>
    @endif
  </div>
</body>
</html>