
@extends('layouts.dashboard')
@section('content')

<!-- Applications Metrics -->
<div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
	<div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-indigo-700">{{ $applications->total() }}</span>
		<span class="text-xs text-gray-500">Total Applications</span>
	</div>
	<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-green-700">{{ $applications->where('status', 'pending')->count() }}</span>
		<span class="text-xs text-gray-500">Pending</span>
	</div>
	<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-blue-700">{{ $applications->where('status', 'accepted')->count() }}</span>
		<span class="text-xs text-gray-500">Accepted</span>
	</div>
	<div class="bg-pink-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-pink-700">{{ $applications->where('status', 'rejected')->count() }}</span>
		<span class="text-xs text-gray-500">Rejected</span>
	</div>
</div>

<!-- Live Search & Filter -->
<div class="mb-6 flex flex-col md:flex-row md:items-center gap-4">
	<input id="app-search" type="text" placeholder="Search by student, opportunity, or status..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" />
	<select id="app-status" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
		<option value="">All Statuses</option>
		<option value="pending">Pending</option>
		<option value="accepted">Accepted</option>
		<option value="rejected">Rejected</option>
	</select>
</div>

<script>
// Simple live filter for demo (client-side)
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('app-search');
	const statusSelect = document.getElementById('app-status');
	const rows = document.querySelectorAll('tbody tr');
	function filterRows() {
		const search = searchInput.value.toLowerCase();
		const status = statusSelect.value;
		rows.forEach(row => {
			const student = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
			const opportunity = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
			const rowStatus = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
			let show = true;
			if (search && !(student.includes(search) || opportunity.includes(search))) show = false;
			if (status && rowStatus !== status) show = false;
			row.style.display = show ? '' : 'none';
		});
	}
	searchInput.addEventListener('input', filterRows);
	statusSelect.addEventListener('change', filterRows);
});
</script>

<!-- Applications Table - Modern Card Layout -->
<div class="bg-white rounded-lg shadow p-6">
	<h2 class="text-2xl font-bold mb-4 text-indigo-700">All Applications</h2>
	<table class="table-auto w-full">
		<thead>
			<tr class="bg-gray-100">
				<th class="px-4 py-2 text-left">#</th>
				<th class="px-4 py-2 text-left">Student</th>
				<th class="px-4 py-2 text-left">Opportunity</th>
				<th class="px-4 py-2 text-left">Status</th>
				<th class="px-4 py-2 text-left">Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($applications as $i => $application)
			<tr class="border-b">
				<td class="px-4 py-2">{{ ($applications->currentPage() - 1) * $applications->perPage() + $i + 1 }}</td>
				<td class="px-4 py-2">{{ $application->student->user->name ?? '-' }}</td>
				<td class="px-4 py-2">{{ $application->opportunity->title ?? '-' }}</td>
				<td class="px-4 py-2">{{ $application->status }}</td>
				<td class="px-4 py-2">
					<a href="{{ route('admin.applications.show', $application->id) }}" class="text-indigo-600 hover:underline">View</a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<!-- Pagination -->
	<div class="mt-6 flex justify-center">
		{{ $applications->links() }}
	</div>
</div>

{{-- End of Applications Index View --}}
@endsection
