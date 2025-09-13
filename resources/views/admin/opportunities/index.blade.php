
@extends('layouts.dashboard')
@section('content')

<!-- Opportunities Metrics -->
<div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
	<div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-indigo-700">{{ $opportunities->total() }}</span>
		<span class="text-xs text-gray-500">Total Opportunities</span>
	</div>
	<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-green-700">{{ $opportunities->where('type', 'internship')->count() }}</span>
		<span class="text-xs text-gray-500">Internships</span>
	</div>
	<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-blue-700">{{ $opportunities->where('type', 'job')->count() }}</span>
		<span class="text-xs text-gray-500">Jobs</span>
	</div>
	<div class="bg-pink-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-pink-700">{{ $opportunities->unique('organization_id')->count() }}</span>
		<span class="text-xs text-gray-500">Organizations</span>
	</div>
</div>

<!-- Live Search & Filter -->
<div class="mb-6 flex flex-col md:flex-row md:items-center gap-4">
	<input id="opp-search" type="text" placeholder="Search by title, organization, or type..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" />
	<select id="opp-type" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
		<option value="">All Types</option>
		<option value="job">Job</option>
		<option value="internship">Internship</option>
	</select>
	<select id="opp-org" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
		<option value="">All Organizations</option>
		@foreach($opportunities->pluck('organization.name')->unique() as $org)
			<option value="{{ $org }}">{{ $org }}</option>
		@endforeach
	</select>
</div>

<script>
// Simple live filter for demo (client-side)
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('opp-search');
	const typeSelect = document.getElementById('opp-type');
	const orgSelect = document.getElementById('opp-org');
	const rows = document.querySelectorAll('tbody tr');
	function filterRows() {
		const search = searchInput.value.toLowerCase();
		const type = typeSelect.value;
		const org = orgSelect.value.toLowerCase();
		rows.forEach(row => {
			const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
			const organization = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
			const rowType = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
			let show = true;
			if (search && !(title.includes(search) || organization.includes(search))) show = false;
			if (type && rowType !== type) show = false;
			if (org && organization !== org) show = false;
			row.style.display = show ? '' : 'none';
		});
	}
	searchInput.addEventListener('input', filterRows);
	typeSelect.addEventListener('change', filterRows);
	orgSelect.addEventListener('change', filterRows);
});
</script>

<!-- Opportunities Table - Modern Card Layout -->
<div class="bg-white rounded-lg shadow p-6">
	<h2 class="text-2xl font-bold mb-4 text-indigo-700">All Opportunities</h2>
	<table class="table-auto w-full">
		<thead>
			<tr class="bg-gray-100">
				<th class="px-4 py-2 text-left">#</th>
				<th class="px-4 py-2 text-left">Title</th>
				<th class="px-4 py-2 text-left">Organization</th>
				<th class="px-4 py-2 text-left">Type</th>
				<th class="px-4 py-2 text-left">Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($opportunities as $i => $opportunity)
			<tr class="border-b">
				<td class="px-4 py-2">{{ ($opportunities->currentPage() - 1) * $opportunities->perPage() + $i + 1 }}</td>
				<td class="px-4 py-2">{{ $opportunity->title }}</td>
				<td class="px-4 py-2">{{ $opportunity->organization->name ?? '-' }}</td>
				<td class="px-4 py-2">{{ $opportunity->type }}</td>
				<td class="px-4 py-2">
					<a href="{{ route('admin.opportunities.show', $opportunity->id) }}" class="text-indigo-600 hover:underline">View</a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<!-- Pagination -->
	<div class="mt-6 flex justify-center">
		{{ $opportunities->links() }}
	</div>
</div>

{{-- End of Opportunities Index View --}}
@endsection
