{{--
    Student Opportunities View
    Shows all active opportunities for the logged-in student.
    Controller: StudentController@opportunities
--}}
@extends('layouts.dashboard')
@section('title', 'My Opportunities')
@section('content')
<div class="max-w-5xl mx-auto py-10">
    <x-ml-loader />
    <h1 class="text-2xl font-bold mb-6">Active Opportunities</h1>
    @if($opportunities->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($opportunities as $opportunity)
                <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between">
                    <div class="flex items-center mb-2">
                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2z" /></svg>
                        </div>
                        <h2 class="text-lg font-semibold">{{ $opportunity->title }}</h2>
                    </div>
                    <div class="text-gray-500 mb-2">{{ $opportunity->organization->name ?? 'Unknown Org' }}</div>
                    <div class="text-sm text-gray-400 mb-2">{{ $opportunity->created_at->diffForHumans() }}</div>
                    <p class="mb-3">{{ Str::limit($opportunity->description, 120) }}</p>
                    <div class="flex justify-between items-center mt-4">
                        <a href="{{ route('opportunities.show', $opportunity->id) }}" class="text-blue-600 hover:underline font-medium">View Details</a>
                        <span class="text-xs text-gray-400">{{ $opportunity->location ?? 'Remote' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $opportunities->links() }}</div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 flex items-center">
            <svg class="w-6 h-6 text-yellow-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01" /></svg>
            No active opportunities found.
        </div>
    @endif
</div>
@endsection
