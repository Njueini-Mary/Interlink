
@extends('layouts.dashboard')
@section('content')

<!-- Activity Edit View - Modern Card Layout -->
<div class="max-w-xl mx-auto py-10">
	<h2 class="text-2xl font-bold mb-6 text-indigo-700">Edit Activity #{{ $activity->id }}</h2>

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
		<form method="POST" action="{{ route('admin.activities.update', $activity->id) }}" class="space-y-6">
			@csrf
			@method('PUT')
			<!-- Action -->
			<div>
				<label for="action" class="block text-sm font-medium text-gray-700">Action</label>
				<input type="text" name="action" id="action" value="{{ old('action', $activity->action) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
			</div>
			<!-- Target Type -->
			<div>
				<label for="target_type" class="block text-sm font-medium text-gray-700">Target Type</label>
				<input type="text" name="target_type" id="target_type" value="{{ old('target_type', $activity->target_type) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
			</div>
			<!-- Target ID -->
			<div>
				<label for="target_id" class="block text-sm font-medium text-gray-700">Target ID</label>
				<input type="text" name="target_id" id="target_id" value="{{ old('target_id', $activity->target_id) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
			</div>
			<div class="flex justify-end">
				<button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Update Activity</button>
			</div>
		</form>
	</div>
</div>

{{-- End of Activity Edit View --}}
@endsection
