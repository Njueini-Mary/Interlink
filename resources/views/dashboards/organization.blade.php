
@extends('layouts.dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Organization Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-10">
    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <a href="{{ route('opportunities.index') }}" class="bg-blue-50 rounded-lg p-6 text-center shadow hover:bg-blue-100 transition flex flex-col justify-center">
            <div class="text-xs text-gray-500 mb-1 flex items-center justify-center"><i class="fas fa-briefcase mr-2 text-blue-400"></i>Open Opportunities</div>
            <div class="text-3xl font-bold text-blue-700">{{ $openOpportunities ?? 0 }}</div>
            <div class="text-xs text-gray-400 mt-2">{{ $opportunitiesChange ?? '+0' }} from last month</div>
        </a>
        <a href="{{ route('applications.index') }}" class="bg-purple-50 rounded-lg p-6 text-center shadow hover:bg-purple-100 transition flex flex-col justify-center">
            <div class="text-xs text-gray-500 mb-1 flex items-center justify-center"><i class="fas fa-user-check mr-2 text-purple-400"></i>Placements Secured</div>
            <div class="text-3xl font-bold text-purple-700">{{ $placementsSecured ?? 0 }}</div>
            <div class="text-xs text-gray-400 mt-2">{{ $placementsChange ?? '+0' }} from last month</div>
        </a>
        <div class="bg-yellow-50 rounded-lg p-6 text-center shadow flex flex-col justify-center">
            <div class="text-xs text-gray-500 mb-1 flex items-center justify-center"><i class="fas fa-star mr-2 text-yellow-400"></i>Organization Rating</div>
            <div class="text-3xl font-bold text-yellow-700">{{ $rating ?? 'N/A' }}/5</div>
        </div>
    </div>

    <!-- Recent Opportunities -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-blue-700">Recent Opportunities</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($opportunities as $opportunity)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
                <div class="font-semibold text-gray-800">{{ $opportunity->title }}</div>
                <div class="text-xs text-gray-500">Applicants: {{ $opportunity->applications_count ?? 0 }}</div>
                <div class="text-xs text-gray-400">Status: {{ ucfirst($opportunity->status) }}</div>
                <div class="mt-2 flex justify-end">
                    <a href="{{ route('opportunities.show', $opportunity->id) }}" class="text-blue-600 hover:underline text-sm">View Details</a>
                </div>
            </div>
            @empty
                <div class="bg-white rounded-lg shadow p-4">No recent opportunities found.</div>
            @endforelse
        </div>
    </div>

    <!-- Recent Student Applications (ML Ranked) -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-purple-700 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Top Matching Applicants
            <span class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded">ML Ranked</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($applications ?? [] as $application)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between {{ isset($application->match_score) && $application->match_score >= 60 ? 'border-l-4 border-green-500' : '' }}">
                <div class="flex justify-between items-start mb-2">
                    <div class="font-semibold text-gray-800">{{ $application->student->name }}</div>
                    @if(isset($application->match_score))
                        <span class="text-xs font-bold px-2 py-1 rounded
                            @if($application->match_score >= 80) bg-green-100 text-green-800
                            @elseif($application->match_score >= 60) bg-blue-100 text-blue-800
                            @elseif($application->match_score >= 40) bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ round($application->match_score) }}% Match
                        </span>
                    @endif
                </div>
                <div class="text-xs text-gray-500">Applied for: {{ $application->opportunity->title }}</div>
                <div class="text-xs text-gray-400">{{ $application->created_at->diffForHumans() }}</div>
                <div class="text-xs text-gray-400">Status: <span class="font-medium">{{ ucfirst($application->status) }}</span></div>
                @if(isset($application->match_score) && $application->match_score >= 60)
                    <div class="text-xs text-green-600 font-medium mt-1">⭐ Highly Recommended</div>
                @endif
                <div class="mt-2 flex justify-end">
                    <a href="{{ route('applications.show', $application->id) }}" class="text-purple-600 hover:underline text-sm">View Application</a>
                </div>
            </div>
            @empty
                <div class="bg-white rounded-lg shadow p-4">No recent student applications.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
