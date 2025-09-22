{{--
    Student Applications View
    Shows all applications submitted by the logged-in student.
    Controller: StudentController@applications
--}}
@extends('layouts.dashboard')
@section('title', 'My Applications')
@section('content')
<div class="max-w-7xl mx-auto py-10 px-6 bg-gray-50 rounded-lg shadow-lg">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-800 text-center">My Applications</h1>

    @if($applications->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($applications as $application)
                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col justify-between">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $application->opportunity->title ?? 'N/A' }}</h2>
                    </div>
                    <p class="text-gray-600 mb-2">Submitted on: <span class="font-medium">{{ $application->created_at->format('M d, Y') }}</span></p>
                    <span class="inline-block px-3 py-1 rounded-full bg-green-50 text-green-700 text-sm font-semibold mb-4">{{ ucfirst($application->status) }}</span>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('opportunities.show', $application->opportunity_id) }}" class="text-blue-600 hover:underline font-medium">View Opportunity</a>
                        <span class="text-sm text-gray-500">{{ $application->organization->name ?? 'N/A' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $applications->links('pagination::tailwind') }}
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg flex items-center">
            <svg class="w-8 h-8 text-yellow-400 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01" />
            </svg>
            <span class="text-gray-700">You have not submitted any applications yet.</span>
        </div>
    @endif
</div>
@endsection
