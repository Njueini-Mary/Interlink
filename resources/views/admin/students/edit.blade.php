
@extends('layouts.dashboard')
@section('content')

<!-- Student Edit View - Modern Card Layout -->
<div class="max-w-xl mx-auto py-10">
	<h2 class="text-2xl font-bold mb-6 text-indigo-700">Edit Student #{{ $student->id }}: {{ $student->user->name ?? '-' }}</h2>

	{{-- Error Display --}}
	@if ($errors->any())
		<div class="mb-6">
			<div class="bg-red-50 border border-red-200 rounded-lg p-4">
				<ul class="list-disc list-inside text-red-600">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		</div>
	@endif

	<div class="bg-gray-50 rounded-lg p-6 shadow">
		<form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="space-y-6">
			@csrf
			@method('PUT')
			<!-- University -->
			<div>
				<label for="university" class="block text-sm font-medium text-gray-700">University</label>
				<input type="text" name="university" id="university" value="{{ old('university', $student->university) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
			</div>
			<!-- Course -->
			<div>
				<label for="course" class="block text-sm font-medium text-gray-700">Course</label>
				<input type="text" name="course" id="course" value="{{ old('course', $student->course) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
			</div>
			<!-- Year -->
			<div>
				<label for="year" class="block text-sm font-medium text-gray-700">Year</label>
				<input type="text" name="year" id="year" value="{{ old('year', $student->year) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
			</div>
			<div class="flex justify-end">
				<button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Update Student</button>
			</div>
		</form>
	</div>
</div>

{{-- End of Student Edit View --}}
@endsection
