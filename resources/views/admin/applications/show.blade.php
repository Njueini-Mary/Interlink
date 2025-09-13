
@extends('layouts.dashboard')
@section('content')

<!-- Application Show View - Modern Card Layout -->
<div class="max-w-xl mx-auto py-10">
	<h2 class="text-2xl font-bold mb-6 text-indigo-700">Application #{{ $application->id }}</h2>

	<!-- Metrics Card -->
	<div class="grid grid-cols-3 gap-4 mb-6">
		<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
			<span class="block text-lg font-bold text-blue-700">{{ $application->student->user->name ?? '-' }}</span>
			<span class="text-xs text-gray-500">Student</span>
		</div>
		<div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
			<span class="block text-lg font-bold text-indigo-700">{{ $application->opportunity->title ?? '-' }}</span>
			<span class="text-xs text-gray-500">Opportunity</span>
		</div>
		<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
			<span class="block text-lg font-bold text-green-700">{{ ucfirst($application->status) }}</span>
			<span class="text-xs text-gray-500">Status</span>
		</div>
	</div>

	<!-- Details Card -->
	<div class="bg-white rounded-lg p-6 shadow mb-6">
		<ul class="space-y-2">
			<li><span class="font-semibold text-gray-700">Student:</span> {{ $application->student->user->name ?? '-' }}</li>
			<li><span class="font-semibold text-gray-700">Opportunity:</span> {{ $application->opportunity->title ?? '-' }}</li>
			<li><span class="font-semibold text-gray-700">Status:</span> {{ ucfirst($application->status) }}</li>
			<li><span class="font-semibold text-gray-700">Submitted:</span> {{ $application->created_at }}</li>
		</ul>
	</div>

	<div class="flex justify-end">
		<a href="{{ route('admin.applications.edit', $application->id) }}" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Edit</a>
	</div>
</div>

{{-- End of Application Show View --}}
@endsection
