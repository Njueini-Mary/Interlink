
@extends('layouts.dashboard')
@section('content')

<!-- Notifications Metrics -->
<div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
	<div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-indigo-700">{{ $notifications->total() }}</span>
		<span class="text-xs text-gray-500">Total Notifications</span>
	</div>
	<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-green-700">{{ $notifications->where('type', 'info')->count() }}</span>
		<span class="text-xs text-gray-500">Info</span>
	</div>
	<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-blue-700">{{ $notifications->where('type', 'success')->count() }}</span>
		<span class="text-xs text-gray-500">Success</span>
	</div>
	<div class="bg-pink-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-pink-700">{{ $notifications->where('type', 'error')->count() }}</span>
		<span class="text-xs text-gray-500">Error</span>
	</div>
</div>

<!-- Live Search & Filter -->
<div class="mb-6 flex flex-col md:flex-row md:items-center gap-4">
	<input id="notif-search" type="text" placeholder="Search by recipient, type, or message..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" />
	<select id="notif-type" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
		<option value="">All Types</option>
		<option value="info">Info</option>
		<option value="success">Success</option>
		<option value="error">Error</option>
	</select>
</div>

<script>
// Simple live filter for demo (client-side)
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('notif-search');
	const typeSelect = document.getElementById('notif-type');
	const rows = document.querySelectorAll('tbody tr');
	function filterRows() {
		const search = searchInput.value.toLowerCase();
		const type = typeSelect.value;
		rows.forEach(row => {
			const recipient = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
			const rowType = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
			const message = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
			let show = true;
			if (search && !(recipient.includes(search) || message.includes(search))) show = false;
			if (type && rowType !== type) show = false;
			row.style.display = show ? '' : 'none';
		});
	}
	searchInput.addEventListener('input', filterRows);
	typeSelect.addEventListener('change', filterRows);
});
</script>

<!-- Notifications Table - Modern Card Layout -->
<div class="bg-white rounded-lg shadow p-6">
	<h2 class="text-2xl font-bold mb-4 text-indigo-700">All Notifications</h2>
	<table class="table-auto w-full">
		<thead>
			<tr class="bg-gray-100">
				<th class="px-4 py-2 text-left">#</th>
				<th class="px-4 py-2 text-left">Recipient</th>
				<th class="px-4 py-2 text-left">Type</th>
				<th class="px-4 py-2 text-left">Message</th>
				<th class="px-4 py-2 text-left">Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($notifications as $i => $notification)
			<tr class="border-b">
				<td class="px-4 py-2">{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $i + 1 }}</td>
				<td class="px-4 py-2">{{ $notification->notifiable->name ?? '-' }}</td>
				<td class="px-4 py-2">{{ $notification->type }}</td>
				<td class="px-4 py-2">{{ $notification->data['message'] ?? '-' }}</td>
				<td class="px-4 py-2">
					<a href="{{ route('admin.notifications.show', $notification->id) }}" class="text-indigo-600 hover:underline">View</a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<!-- Pagination -->
	<div class="mt-6 flex justify-center">
		{{ $notifications->links() }}
	</div>
</div>

{{-- End of Notifications Index View --}}
@endsection
