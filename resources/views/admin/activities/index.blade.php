
@extends('layouts.dashboard')
@section('content')

<!-- Activities Metrics -->
<div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
	<div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-indigo-700">{{ $activities->total() }}</span>
		<span class="text-xs text-gray-500">Total Activities</span>
	</div>
	<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-green-700">{{ $activities->where('action', 'created')->count() }}</span>
		<span class="text-xs text-gray-500">Created</span>
	</div>
	<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-blue-700">{{ $activities->where('action', 'updated')->count() }}</span>
		<span class="text-xs text-gray-500">Updated</span>
	</div>
	<div class="bg-pink-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-pink-700">{{ $activities->where('action', 'deleted')->count() }}</span>
		<span class="text-xs text-gray-500">Deleted</span>
	</div>
</div>

<!-- Live Search & Filter -->
<div class="mb-6 flex flex-col md:flex-row md:items-center gap-4">
	<input id="act-search" type="text" placeholder="Search by user, action, or target..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" />
	<select id="act-action" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
		<option value="">All Actions</option>
		<option value="created">Created</option>
		<option value="updated">Updated</option>
		<option value="deleted">Deleted</option>
	</select>
</div>

<script>
// Simple live filter for demo (client-side)
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('act-search');
	const actionSelect = document.getElementById('act-action');
	const rows = document.querySelectorAll('tbody tr');
	function filterRows() {
		const search = searchInput.value.toLowerCase();
		const action = actionSelect.value;
		rows.forEach(row => {
			const user = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
			const rowAction = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
			const target = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
			let show = true;
			if (search && !(user.includes(search) || target.includes(search))) show = false;
			if (action && rowAction !== action) show = false;
			row.style.display = show ? '' : 'none';
		});
	}
	searchInput.addEventListener('input', filterRows);
	actionSelect.addEventListener('change', filterRows);
});
</script>

<!-- Activities Table - Modern Card Layout -->
<div class="bg-white rounded-lg shadow p-6">
	<h2 class="text-2xl font-bold mb-4 text-indigo-700">All Activities</h2>
	<table class="table-auto w-full">
		<thead>
			<tr class="bg-gray-100">
				<th class="px-4 py-2 text-left">#</th>
				<th class="px-4 py-2 text-left">User</th>
				<th class="px-4 py-2 text-left">Action</th>
				<th class="px-4 py-2 text-left">Target</th>
				<th class="px-4 py-2 text-left">Created</th>
				<th class="px-4 py-2 text-left">Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($activities as $i => $activity)
			<tr class="border-b">
				<td class="px-4 py-2">{{ ($activities->currentPage() - 1) * $activities->perPage() + $i + 1 }}</td>
				<td class="px-4 py-2">{{ $activity->user->name ?? '-' }}</td>
				<td class="px-4 py-2">{{ $activity->action }}</td>
				<td class="px-4 py-2">{{ $activity->target_type }}: {{ $activity->target_id }}</td>
				<td class="px-4 py-2">{{ $activity->created_at }}</td>
				<td class="px-4 py-2">
					<a href="{{ route('admin.activities.show', $activity->id) }}" class="text-indigo-600 hover:underline">View</a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<!-- Pagination -->
	<div class="mt-6 flex justify-center">
		{{ $activities->links() }}
	</div>
</div>

{{-- End of Activities Index View --}}
@endsection
