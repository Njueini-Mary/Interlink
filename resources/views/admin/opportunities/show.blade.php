@extends('layouts.dashboard')
@section('content')


@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
    <strong class="font-bold">Success!</strong>
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

<!-- Opportunity Show View - Modern Card Layout with Gradient Background -->
<div class="max-w-4xl mx-auto py-10 px-6 bg-gradient-to-r from-indigo-50 via-white to-indigo-100 rounded-lg shadow-lg">
    <h2 class="text-3xl font-extrabold mb-8 text-indigo-800 text-center">Opportunity Details</h2>

    <!-- Metrics Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-100 rounded-lg p-4 text-center shadow-md">
            <span class="block text-xl font-semibold text-blue-800">{{ $opportunity->organization->name ?? 'N/A' }}</span>
            <span class="text-sm text-gray-600">Organization</span>
        </div>
        <div class="bg-indigo-100 rounded-lg p-4 text-center shadow-md">
            <span class="block text-xl font-semibold text-indigo-800">{{ ucfirst($opportunity->type) }}</span>
            <span class="text-sm text-gray-600">Type</span>
        </div>
        <div class="bg-green-100 rounded-lg p-4 text-center shadow-md">
            <span class="block text-xl font-semibold text-green-800">{{ $opportunity->created_at->format('Y-m-d') }}</span>
            <span class="text-sm text-gray-600">Created</span>
        </div>
    </div>

    <!-- Details Section -->
    <div class="bg-white rounded-lg p-8 shadow-md mb-8">
        <h3 class="text-2xl font-bold mb-4 text-gray-800">Details</h3>
        <ul class="space-y-4">
            <li><strong class="text-gray-700">Organization:</strong> {{ $opportunity->organization->name ?? 'N/A' }}</li>
            <li><strong class="text-gray-700">Category:</strong> {{ $opportunity->category->name ?? 'N/A' }}</li>
            <li><strong class="text-gray-700">Type:</strong> {{ ucfirst($opportunity->type) }}</li>
            <li><strong class="text-gray-700">Description:</strong> {{ $opportunity->description }}</li>
            <li><strong class="text-gray-700">Posted By:</strong> {{ $opportunity->user->name ?? 'N/A' }}</li>
            <li><strong class="text-gray-700">Skills Required:</strong>
                @if($opportunity->skills->isNotEmpty())
                    <ul class="list-disc pl-6 mt-2">
                        @foreach($opportunity->skills as $skill)
                            <li>{{ $skill->name }}</li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-gray-500">None</span>
                @endif
            </li>
        </ul>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end">
        <a href="{{ route('admin.opportunities.edit', $opportunity->id) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 transition">Edit Opportunity</a>
    </div>
</div>

{{-- End of Opportunity Show View --}}
@endsection
