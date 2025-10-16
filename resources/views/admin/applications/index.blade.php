
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
<div class="mb-6">
	<table class="min-w-full bg-white rounded-lg shadow">
		<thead>
			<tr>
				<th class="px-4 py-2">ID</th>
				<th class="px-4 py-2">Student</th>
				<th class="px-4 py-2">Opportunity</th>
				<th class="px-4 py-2">Status</th>
				<th class="px-4 py-2">ML Match Score</th>
			</tr>
		</thead>
		<tbody>
			@foreach($applications as $application)
			<tr>
				<td class="border px-4 py-2">{{ $application->id }}</td>
				<td class="border px-4 py-2">{{ $application->student->user->name ?? '-' }}</td>
				<td class="border px-4 py-2">{{ $application->opportunity->title ?? '-' }}</td>
				<td class="border px-4 py-2">{{ ucfirst($application->status) }}</td>
				<td class="border px-4 py-2">
						@if(isset($application->match_score))
							<span class="font-bold {{ $application->match_score >= 80 ? 'text-green-600' : ($application->match_score >= 60 ? 'text-blue-600' : ($application->match_score >= 40 ? 'text-yellow-600' : 'text-red-600')) }}">
								{{ round($application->match_score) }}%
							</span>
							<span class="text-xs text-gray-500 ml-2">
								@if($application->match_score >= 80) Excellent Match
								@elseif($application->match_score >= 60) Good Match
								@elseif($application->match_score >= 40) Moderate Match
								@else Low Match
								@endif
							</span>
							<form method="POST" action="{{ route('admin.applications.generate_match', $application->id) }}" class="mt-2">
								@csrf
								<button type="submit" class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600 text-xs">Regenerate Match</button>
							</form>
						@else
							<form method="POST" action="{{ route('admin.applications.generate_match', $application->id) }}">
								@csrf
								<button type="submit" class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600 text-xs">Generate Match</button>
							</form>
						@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

{{-- End of Applications Index View --}}
@endsection
