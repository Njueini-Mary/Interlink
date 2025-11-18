
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

<!-- Export Buttons -->
<div class="mb-6 flex gap-4">
	<a href="{{ route('admin.applications.export', ['type' => 'excel']) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Export Excel</a>
	<a href="{{ route('admin.applications.export', ['type' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Export PDF</a>
</div>

<!-- ML Loader -->
<x-ml-loader />

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
			<tr class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider">
				<th class="px-4 py-3 rounded-tl-lg">#</th>
				<th class="px-4 py-3">Student</th>
				<th class="px-4 py-3">Opportunity</th>
				<th class="px-4 py-3">Status</th>
				<th class="px-4 py-3">ML Match Score</th>
				<th class="px-4 py-3">Applicant Skills</th>
				<th class="px-4 py-3 rounded-tr-lg">Requirements</th>
			</tr>
		</thead>
		<tbody>
			@foreach($applications as $application)
			<tr class="hover:bg-indigo-50 transition-colors">
				<td class="border px-4 py-3 text-center font-semibold text-gray-500">{{ $loop->iteration }}</td>
				<td class="border px-4 py-3 font-medium text-gray-900">{{ $application->student->user->name ?? '-' }}</td>
				<td class="border px-4 py-3">{{ $application->opportunity->title ?? '-' }}</td>
				<td class="border px-4 py-3">
					<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
						{{ $application->status === 'accepted' ? 'bg-green-100 text-green-700' : ($application->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
						{{ ucfirst($application->status) }}
					</span>
				</td>
				<td class="border px-4 py-3">
					@php
						$matcher = app(\App\Services\SkillMatcher::class);
						$studentSkills = $application->student && $application->student->skills ? $application->student->skills->pluck('name')->toArray() : [];
						$opportunitySkills = $application->opportunity && $application->opportunity->skills ? $application->opportunity->skills->pluck('name')->toArray() : [];
						$matchedSkills = $matcher->getMatchedSkills($opportunitySkills, $studentSkills);
						$missingSkills = $matcher->getMissingSkills($opportunitySkills, $studentSkills);
						$matchMessage = $matcher->getMatchMessage(
							$application->rf_score ?? $application->match_score ?? 0,
							$matchedSkills,
							$missingSkills,
							$opportunitySkills,
							$application->student->course ?? null,
							$application->student->academic_field ?? null,
							$application->student->gpa ?? null,
							$application->student->interests ? (is_array($application->student->interests) ? $application->student->interests : explode(',', $application->student->interests)) : null,
							'admin'
						);
					@endphp
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
						<div class="mt-2 text-sm text-gray-700 bg-gray-50 rounded p-2">
							{{ $matchMessage ? __(str_replace(['Fair match.', 'Low match.', 'Excellent match.', 'Good match.', 'Moderate match.'], ['This applicant is a good fit for this opportunity.', 'This applicant may need to improve their skills for this opportunity.', 'This applicant is an excellent fit for this opportunity!', 'This applicant is a strong candidate for this opportunity.', 'This applicant is a decent fit for this opportunity.'], $matchMessage)) : 'This applicant may be a good fit.' }}
						</div>
						<form method="POST" action="{{ route('admin.applications.generate_match', $application->id) }}" class="mt-2" onsubmit="showMLLoader()">
							@csrf
							<button type="submit" class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600 text-xs">Regenerate Match</button>
						</form>
					@else
						<form method="POST" action="{{ route('admin.applications.generate_match', $application->id) }}" onsubmit="showMLLoader()">
							@csrf
							<button type="submit" class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600 text-xs">Generate Match</button>
						</form>
					@endif
				</td>
				<td class="border px-4 py-2">
					@foreach($studentSkills as $skill)
						@if(in_array($skill, $matchedSkills))
							<span class="inline-block bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded mr-1 mb-1 font-semibold shadow-sm">{{ $skill }}</span>
						@else
							<span class="inline-block bg-gray-100 text-gray-600 border border-gray-200 px-2 py-1 rounded mr-1 mb-1">{{ $skill }}</span>
						@endif
					@endforeach
				</td>
				<td class="border px-4 py-2">
					@foreach($opportunitySkills as $skill)
						@if(in_array($skill, $matchedSkills))
							<span class="inline-block bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded mr-1 mb-1 font-semibold shadow-sm">{{ $skill }}</span>
						@elseif(in_array($skill, $missingSkills))
							<span class="inline-block bg-red-50 text-red-700 border border-red-200 px-2 py-1 rounded mr-1 mb-1 font-bold shadow-sm animate-pulse">{{ $skill }}</span>
						@else
							<span class="inline-block bg-gray-100 text-gray-600 border border-gray-200 px-2 py-1 rounded mr-1 mb-1">{{ $skill }}</span>
						@endif
					@endforeach
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

{{-- End of Applications Index View --}}
@endsection
