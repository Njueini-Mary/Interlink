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
    <h1 class="text-3xl font-extrabold text-center mb-6 text-blue-800 tracking-tight">
      @if(auth()->user()->role === 'organization')
        <i class="fas fa-briefcase mr-2"></i> My Opportunity Listings
      @else
        <i class="fas fa-list mr-2"></i> All Opportunities
      @endif
    </h1>

    <!-- Filters -->
    <form method="GET" action="{{ route('opportunities.search') }}" class="bg-white p-4 rounded-xl shadow-lg mb-6 border border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
          <select name="category" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
            <option value="">All Categories</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
          <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
            <option value="">All Types</option>
            <option value="WBL" {{ request('type') == 'WBL' ? 'selected' : '' }}>Work-Based Learning</option>
            <option value="SBL" {{ request('type') == 'SBL' ? 'selected' : '' }}>Service-Based Learning</option>
          </select>
        </div>
        
        @if(auth()->user()->role === 'student')
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
          <select name="sort" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-purple-200">
            <option value="">Latest</option>
            <option value="match" {{ request('sort') == 'match' ? 'selected' : '' }}>Best Match (ML)</option>
          </select>
        </div>
        @endif
        
        <div class="flex items-end">
          <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white px-4 py-2 rounded-lg shadow hover:from-blue-600 hover:to-blue-800 font-semibold transition-all">
            <i class="fas fa-filter mr-2"></i>Apply Filters
          </button>
        </div>
      </div>
    </form>

    @if(auth()->user()->role === 'student')
    <form method="POST" action="{{ route('opportunities.calculate_matches') }}" class="mb-6">
      @csrf
      <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-700">Calculate Matches (ML)</button>
    </form>
    @endif
    <!-- Opportunity Listings -->
    @if ($opportunities->count())
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($opportunities as $opportunity)
      <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-2xl transition {{ isset($opportunity->match_score) && $opportunity->match_score >= 60 ? 'border-l-4 border-purple-500' : '' }}">
      @if(isset($opportunity->match_score) && auth()->user()->role === 'student')
        <div class="mb-2">
          <span class="font-bold {{ $opportunity->match_score >= 80 ? 'text-green-600' : ($opportunity->match_score >= 60 ? 'text-blue-600' : ($opportunity->match_score >= 40 ? 'text-yellow-600' : 'text-red-600')) }}">
            {{ round($opportunity->match_score) }}% Match
          </span>
          <span class="text-xs text-gray-500 ml-2">
            @if($opportunity->match_score >= 80) Excellent Match
            @elseif($opportunity->match_score >= 60) Good Match
            @elseif($opportunity->match_score >= 40) Moderate Match
            @else Low Match
            @endif
          </span>
          <div class="mt-2 text-xs text-gray-700">
            <strong>TF-IDF:</strong> {{ round($opportunity->tfidf_score ?? 0) }}%
            <strong>LogReg:</strong> {{ round($opportunity->logreg_score ?? 0) }}%
            <strong>RF:</strong> {{ round($opportunity->rf_score ?? 0) }}%
          </div>
        </div>
      @endif
            <div class="flex justify-between items-start mb-2">
              <h2 class="text-xl font-bold text-blue-700 flex items-center">
                <i class="fas fa-rocket mr-2"></i>{{ $opportunity->title }}
              </h2>
              @if(isset($opportunity->match_score) && auth()->user()->role === 'student')
                <span class="text-xs font-bold px-2 py-1 rounded
                  @if($opportunity->match_score >= 80) bg-green-100 text-green-800
                  @elseif($opportunity->match_score >= 60) bg-blue-100 text-blue-800
                  @elseif($opportunity->match_score >= 40) bg-yellow-100 text-yellow-800
                  @else bg-gray-100 text-gray-800
                  @endif">
                  {{ round($opportunity->match_score) }}% Match
                </span>
              @endif
            </div>
            <p class="text-gray-700 mb-4 italic">{{ \Illuminate\Support\Str::limit($opportunity->description, 150) }}</p>
            <div class="text-sm text-gray-600 mb-4 flex flex-wrap gap-2">
              <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                <i class="fas fa-briefcase mr-1"></i> {{ ucfirst($opportunity->type) }}
              </span>
              <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                <i class="fas fa-layer-group mr-1"></i> {{ $opportunity->category->name ?? 'N/A' }}
              </span>
              <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">
                <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($opportunity->deadline)->toFormattedDateString() }}
              </span>
            </div>
            <div class="flex justify-between gap-2 mt-2">
              @if (auth()->user()->role === 'organization' && isset($opportunity->organization_id) && (auth()->user()->organizationProfile && auth()->user()->organizationProfile->id === $opportunity->organization_id))
                <a href="{{ route('admin.opportunities.edit', $opportunity->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow hover:bg-yellow-600 flex items-center"><i class="fas fa-edit mr-1"></i> Edit</a>
                <form action="{{ route('admin.opportunities.destroy', $opportunity->id) }}" method="POST" class="inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 flex items-center"><i class="fas fa-trash mr-1"></i> Delete</button>
                </form>
              @elseif (auth()->user()->role === 'student')
                <a href="{{ route('opportunities.show', $opportunity->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 flex items-center"><i class="fas fa-eye mr-1"></i> View & Apply</a>
              @else
                <a href="{{ route('opportunities.show', $opportunity->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 flex items-center"><i class="fas fa-eye mr-1"></i> View</a>
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