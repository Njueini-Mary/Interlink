
@extends('layouts.dashboard')
@section('content')

<!-- Student Show View - Modern Card Layout -->
<div class="max-w-xl mx-auto py-10">
	<h2 class="text-2xl font-bold mb-6 text-indigo-700">Student #{{ $student->id }}: {{ $student->user->name ?? '-' }}</h2>

	<!-- Metrics Card -->
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
			<span class="block text-lg font-bold text-green-700">{{ $student->year }}</span>
			<span class="text-xs text-gray-500">Year</span>
		</div>
	</div>

	<!-- Details Card -->
	<div class="bg-white rounded-lg p-6 shadow mb-6">
		<ul class="space-y-2">
			<li><span class="font-semibold text-gray-700">University:</span> {{ $student->university }}</li>
			<li><span class="font-semibold text-gray-700">Course:</span> {{ $student->course }}</li>
			<li><span class="font-semibold text-gray-700">Year:</span> {{ $student->year }}</li>
		</ul>
	</div>

	<div class="flex justify-end">
		<a href="{{ route('admin.students.edit', $student->id) }}" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Edit</a>
	</div>
</div>

{{-- End of Student Show View --}}
@endsection
