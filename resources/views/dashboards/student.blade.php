
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