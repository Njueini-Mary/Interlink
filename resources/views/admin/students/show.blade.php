@extends('layouts.dashboard')
@section('content')

<!-- Student Show View - Modern Card Layout -->
<div class="max-w-2xl mx-auto py-10">
    <h2 class="text-2xl font-bold mb-6 text-indigo-700">{{ $student->user->name ?? 'Student' }}</h2>
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
            <span class="block text-lg font-bold text-blue-700">{{ $student->university }}</span>
            <span class="text-xs text-gray-500">University</span>
        </div>
        <div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
            <span class="block text-lg font-bold text-indigo-700">{{ $student->course }}</span>
            <span class="text-xs text-gray-500">Course</span>
        </div>
        <div class="bg-green-50 rounded-lg px-4 py-2 text-center">
            <span class="block text-lg font-bold text-green-700">Year {{ $student->year }}</span>
            <span class="text-xs text-gray-500">Year</span>
        </div>
    </div>
    <div class="bg-white rounded-lg p-6 shadow mb-6">
        <ul class="space-y-2">
            <li><span class="font-semibold text-gray-700">Email:</span> {{ $student->user->email ?? '-' }}</li>
            <li><span class="font-semibold text-gray-700">Academic Field:</span> {{ $student->academic_field ?? '-' }}</li>
            <li><span class="font-semibold text-gray-700">Available:</span> <span class="badge {{ $student->is_available ? 'bg-success' : 'bg-secondary' }}">{{ $student->is_available ? 'Yes' : 'No' }}</span></li>
            <li><span class="font-semibold text-gray-700">Skills:</span> <span class="text-sm">@foreach($student->skills as $skill)<span class="inline-block bg-indigo-100 text-indigo-700 rounded px-2 py-1 mr-1 mb-1">{{ $skill->name }}</span>@endforeach</span></li>
            <li><span class="font-semibold text-gray-700">Rating:</span> {{ number_format($student->rating, 1) }} / 5 ({{ $student->rating_count ?? 0 }} ratings)</li>
            <li><span class="font-semibold text-gray-700">Bio:</span> <span class="text-sm text-gray-600">{{ $student->bio ?? '-' }}</span></li>
            <li><span class="font-semibold text-gray-700">Resume:</span> @if($student->resume)<a href="{{ asset('storage/' . $student->resume) }}" target="_blank" class="text-indigo-600 hover:underline">View Resume (PDF)</a>@else <span class="text-gray-400">Not uploaded</span>@endif</li>
        </ul>
    </div>
    <div class="flex justify-between items-center mt-6">
        <a href="{{ route('admin.students.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md shadow hover:bg-gray-300">Back</a>
        <a href="{{ route('admin.students.edit', $student->id) }}" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Edit</a>
    </div>
</div>

{{-- End of Student Show View --}}
@endsection
