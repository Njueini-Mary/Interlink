
@extends('layouts.dashboard')
@section('content')

<!-- Notification Edit View - Modern Card Layout -->
<div class="max-w-xl mx-auto py-10">
	<h2 class="text-2xl font-bold mb-6 text-indigo-700">Edit Notification #{{ $notification->id }}</h2>

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
		<form method="POST" action="{{ route('admin.notifications.update', $notification->id) }}" class="space-y-6">
			@csrf
			@method('PUT')
			<!-- Type -->
			<div>
				<label for="type" class="block text-sm font-medium text-gray-700">Type</label>
				<input type="text" name="type" id="type" value="{{ old('type', $notification->type) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
			</div>
			<!-- Message -->
			<div>
				<label for="message" class="block text-sm font-medium text-gray-700">Message</label>
				<input type="text" name="message" id="message" value="{{ old('message', $notification->data['message'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
			</div>
			<div class="flex justify-end">
				<button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Update Notification</button>
			</div>
		</form>
	</div>
</div>

{{-- End of Notification Edit View --}}
@endsection
