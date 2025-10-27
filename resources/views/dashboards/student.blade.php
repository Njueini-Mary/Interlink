
@extends('layouts.dashboard')


@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Student Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto py-10">
    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
        <a href="{{ route('student.opportunities') }}" class="bg-blue-50 rounded-lg p-6 text-center shadow hover:bg-blue-100 transition">
            <div class="text-xs text-gray-500 mb-1">Active Opportunities</div>
            <div class="text-2xl font-bold text-blue-700">{{ $activeOpportunities ?? 0 }}</div>
            <div class="text-xs text-gray-400 mt-2">{{ $opportunitiesChange ?? '+0' }} from last month</div>
        </a>
        <a href="{{ route('student.applications') }}" class="bg-green-50 rounded-lg p-6 text-center shadow hover:bg-green-100 transition">
            <div class="text-xs text-gray-500 mb-1">Applications Submitted</div>
            <div class="text-2xl font-bold text-green-700">{{ $applicationsSubmitted ?? 0 }}</div>
            <div class="text-xs text-gray-400 mt-2">{{ $applicationsChange ?? '+0' }} from last month</div>
        </a>
        <a href="{{ route('student.interviews') }}" class="bg-purple-50 rounded-lg p-6 text-center shadow hover:bg-purple-100 transition">
            <div class="text-xs text-gray-500 mb-1">Interviews Scheduled</div>
            <div class="text-2xl font-bold text-purple-700">{{ $interviewsScheduled ?? 0 }}</div>
            <div class="text-xs text-gray-400 mt-2">{{ $interviewsChange ?? '+0' }} from last month</div>
        </a>
        <a href="{{ route('student.placements') }}" class="bg-orange-50 rounded-lg p-6 text-center shadow hover:bg-orange-100 transition">
            <div class="text-xs text-gray-500 mb-1">Placements Secured</div>
            <div class="text-2xl font-bold text-orange-700">{{ $placementsSecured ?? 0 }}</div>
            <div class="text-xs text-gray-400 mt-2">{{ $placementsChange ?? '+0' }} from last month</div>
        </a>
    </div>

        @if(auth()->user()->role === 'student')
        <form method="POST" action="{{ route('student.dashboard.calculate_matches') }}" class="mb-10">
            @csrf
            <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-700">Calculate Matches (ML)</button>
        </form>
        @endif
        <!-- ML Recommended Opportunities -->
    @if(!empty($recommendedOpportunities) && count($recommendedOpportunities) > 0)
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-indigo-700 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Recommended for You
            @if($mlModelActive ?? false)
                <span class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded">ML Powered</span>
            @endif
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($recommendedOpportunities as $match)
            <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg shadow-md p-4 border-l-4 border-purple-500">
                <div class="flex justify-between items-start mb-2">
                    <div class="font-semibold text-gray-800">{{ $match['opportunity']->title }}</div>
                    <span class="text-xs font-bold px-2 py-1 rounded 
                        @if($match['match_score'] >= 80) bg-green-100 text-green-800
                        @elseif($match['match_score'] >= 60) bg-blue-100 text-blue-800
                        @elseif($match['match_score'] >= 40) bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $match['match_score'] }}% Match
                    </span>
                </div>
                <div class="text-xs text-gray-600 mb-2">{{ $match['opportunity']->organization->name }}</div>
                <div class="text-xs text-gray-500 mb-2">{{ Str::limit($match['opportunity']->description, 100) }}</div>
                
                @if(!empty($match['matched_skills']))
                <div class="mb-2">
                    <div class="text-xs text-gray-500 mb-1">Matched Skills:</div>
                    <div class="flex flex-wrap gap-1">
                        @foreach(array_slice($match['matched_skills'], 0, 3) as $skill)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="flex justify-between items-center mt-3">
                    <span class="text-xs text-purple-600 font-medium">{{ $match['match_message'] }}</span>
                    <a href="{{ route('opportunities.show', $match['opportunity']->id) }}" class="text-xs bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- My Applications -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-indigo-700">My Applications</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($applications ?? [] as $application)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="font-semibold text-gray-800">{{ $application->opportunity->title }}</div>
                <div class="text-xs text-gray-500">Organization: {{ $application->opportunity->organization->name }}</div>
                <div class="text-xs text-gray-400">Status: {{ ucfirst($application->status) }}</div>
                <div class="text-xs text-gray-400">Applied: {{ $application->created_at->format('M d, Y') }}</div>
            </div>
            @endforeach
            @if(empty($applications) || count($applications) == 0)
                <div class="bg-white rounded-lg shadow p-4">No applications found.</div>
            @endif
        </div>
    </div>
    <!-- Upcoming Interviews -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-indigo-700">Upcoming Interviews</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($interviews ?? [] as $interview)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="font-semibold text-gray-800">{{ $interview->opportunity->title }}</div>
                <div class="text-xs text-gray-500">Date: {{ $interview->scheduled_at->format('M d, Y H:i') }}</div>
                <div class="text-xs text-gray-400">Status: {{ ucfirst($interview->status) }}</div>
            </div>
            @endforeach
            @if(empty($interviews) || count($interviews) == 0)
                <div class="bg-white rounded-lg shadow p-4">No upcoming interviews scheduled.</div>
            @endif
        </div>
    </div>
    <!-- Placement Status -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-indigo-700">Placement Status</h3>
        <div class="bg-white rounded-lg shadow p-4">
            @if($placementsSecured ?? 0 > 0)
                <span class="inline-block bg-green-200 text-green-800 px-3 py-1 rounded font-semibold mr-2">Placed</span>
                <span class="text-xs text-gray-500">You have secured {{ $placementsSecured }} placement(s).</span>
            @else
                <span class="inline-block bg-yellow-200 text-yellow-800 px-3 py-1 rounded font-semibold mr-2">Seeking Placement</span>
                <span class="text-xs text-gray-500">No placements secured yet.</span>
            @endif
        </div>
    </div>
    <!-- Recent Messages from Organizations -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-indigo-700">Recent Messages from Organizations</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($messages ?? [] as $message)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="font-semibold text-gray-800">{{ $message->organization->name }}</div>
                <div class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</div>
                <div class="text-xs text-gray-400">{{ $message->content }}</div>
            </div>
            @endforeach
            @if(empty($messages) || count($messages) == 0)
                <div class="bg-white rounded-lg shadow p-4">No recent messages from organizations.</div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection