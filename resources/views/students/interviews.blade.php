{{--
    Student Interviews View
    Shows all interviews scheduled for the logged-in student.
    Controller: StudentController@interviews
--}}
@extends('layouts.dashboard')
@section('title', 'My Interviews')
@section('content')
<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Interviews Scheduled</h1>
    @if($interviews->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($interviews as $interview)
                <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between">
                    <div class="flex items-center mb-2">
                        <div class="bg-purple-100 rounded-full p-2 mr-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <h2 class="text-lg font-semibold">{{ $interview->opportunity->title ?? 'N/A' }}</h2>
                    </div>
                    <div class="text-gray-500 mb-2">Interview Date: <span class="font-medium">{{ $interview->interview_date ? $interview->interview_date->format('M d, Y') : 'TBD' }}</span></div>
                    <div class="mb-2"><span class="inline-block px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold">{{ ucfirst($interview->status) }}</span></div>
                    <div class="flex justify-between items-center mt-4">
                        <a href="{{ route('opportunities.show', $interview->opportunity_id) }}" class="text-blue-600 hover:underline font-medium">View Opportunity</a>
                        <span class="text-xs text-gray-400">{{ $interview->organization->name ?? '' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $interviews->links() }}</div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 flex items-center">
            <svg class="w-6 h-6 text-yellow-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01" /></svg>
            No interviews scheduled yet.
        </div>
    @endif
</div>
@endsection
