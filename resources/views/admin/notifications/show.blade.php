
@extends('layouts.dashboard')
@section('content')

<!-- Notification Show View - Modern Card Layout -->
<div class="max-w-xl mx-auto py-10">
	<h2 class="text-2xl font-bold mb-6 text-indigo-700">Notification #{{ $notification->id }}</h2>

	<!-- Metrics Card -->
	<div class="grid grid-cols-3 gap-4 mb-6">
		<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
			<span class="block text-lg font-bold text-blue-700">{{ $notification->notifiable->name ?? '-' }}</span>
			<span class="text-xs text-gray-500">Recipient</span>
		</div>
		<div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
			<span class="block text-lg font-bold text-indigo-700">{{ ucfirst($notification->type) }}</span>
			<span class="text-xs text-gray-500">Type</span>
		</div>
		<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
			<span class="block text-lg font-bold text-green-700">{{ $notification->created_at }}</span>
			<span class="text-xs text-gray-500">Created</span>
		</div>
	</div>

	<!-- Details Card -->
	<div class="bg-white rounded-lg p-6 shadow mb-6">
		<ul class="space-y-2">
			<li><span class="font-semibold text-gray-700">Recipient:</span> {{ $notification->notifiable->name ?? '-' }}</li>
			<li><span class="font-semibold text-gray-700">Type:</span> {{ ucfirst($notification->type) }}</li>
			<li><span class="font-semibold text-gray-700">Message:</span> {{ $notification->data['message'] ?? '-' }}</li>
			<li><span class="font-semibold text-gray-700">Created:</span> {{ $notification->created_at }}</li>
		</ul>
	</div>

	<div class="flex justify-end">
		<a href="{{ route('admin.notifications.edit', $notification->id) }}" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Edit</a>
	</div>
</div>

{{-- End of Notification Show View --}}
@endsection
