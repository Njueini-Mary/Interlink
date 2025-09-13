{{--
    Student Placements View
    Shows all placements for the logged-in student.
    Controller: StudentController@placements
--}}
@extends('layouts.dashboard')
@section('title', 'My Placements')
@section('content')
<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Placements</h1>
    @if($placements->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($placements as $placement)
                <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between">
                    <div class="flex items-center mb-2">
                        <div class="bg-green-100 rounded-full p-2 mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <h2 class="text-lg font-semibold">{{ $placement->opportunity->title ?? 'N/A' }}</h2>
                    </div>
                    <div class="text-gray-500 mb-2">Placed on: <span class="font-medium">{{ $placement->updated_at->format('M d, Y') }}</span></div>
                    <div class="mb-2"><span class="inline-block px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">{{ ucfirst($placement->status) }}</span></div>
                    <div class="flex justify-between items-center mt-4">
                        <a href="{{ route('opportunities.show', $placement->opportunity_id) }}" class="text-blue-600 hover:underline font-medium">View Opportunity</a>
                        <span class="text-xs text-gray-400">{{ $placement->organization->name ?? '' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $placements->links() }}</div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 flex items-center">
            <svg class="w-6 h-6 text-yellow-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01" /></svg>
            No placements found yet.
        </div>
    @endif
</div>
@endsection
