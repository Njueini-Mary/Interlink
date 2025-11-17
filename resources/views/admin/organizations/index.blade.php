@extends('layouts.dashboard')
@section('content')
<div class="max-w-5xl mx-auto py-8">
	<!-- Metrics Section -->

		<!-- Metrics Section -->
		<div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
			<div class="bg-indigo-50 px-6 py-4 rounded-lg shadow flex flex-col items-center">
				<span class="text-3xl font-bold text-indigo-700">{{ \App\Models\Opportunity::count() }}</span>
				<span class="text-sm text-gray-600">Total Opportunities</span>
			</div>
			<div class="bg-green-50 px-6 py-4 rounded-lg shadow flex flex-col items-center">
				<span class="text-3xl font-bold text-green-700">{{ \App\Models\Application::count() }}</span>
				<span class="text-sm text-gray-600">Total Applications</span>
			</div>
			<div class="bg-yellow-50 px-6 py-4 rounded-lg shadow flex flex-col items-center">
				<span class="text-3xl font-bold text-yellow-700">{{ \App\Models\Organization::count() }}</span>
				<span class="text-sm text-gray-600">Total Organizations</span>
			</div>
			<div class="bg-pink-50 px-6 py-4 rounded-lg shadow flex flex-col items-center">
				<span class="text-3xl font-bold text-pink-700">{{ \App\Models\Student::count() }}</span>
				<span class="text-sm text-gray-600">Total Students</span>
			</div>
		</div>

	<!-- Filter/Search Bar -->
		<!-- Filter/Search Bar (live filtering with JS) -->
		<form method="GET" action="" id="orgFilterForm" class="mb-4 flex flex-col md:flex-row gap-2 md:gap-4 items-center">
			<input type="text" name="search" id="orgSearchInput" value="{{ request('search') }}" placeholder="Search by name, email, location, industry..." class="px-4 py-2 border rounded-md w-full md:w-1/3 focus:ring-indigo-500 focus:border-indigo-500" />
			<button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
			<a href="{{ route('admin.organizations.index') }}" class="text-gray-500 hover:underline">Reset</a>
			<a href="{{ route('admin.organizations.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Add Organization</a>
		</form>

	<!-- Organizations Table -->
		<div class="overflow-x-auto">
			<table class="min-w-full bg-white rounded-lg shadow">
				<thead class="bg-gray-100">
					<tr>
						<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Email</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Industry</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opportunities</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applications</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
					</tr>
				</thead>
				<tbody>
					@php
						$start = ($organizations->currentPage() - 1) * $organizations->perPage();
					@endphp
					@forelse($organizations as $org)
					<tr class="border-b hover:bg-gray-50">
						<td class="px-4 py-4 whitespace-nowrap text-gray-500">{{ $loop->iteration + $start }}</td>
						<td class="px-6 py-4 whitespace-nowrap">{{ $org->name }}</td>
						<td class="px-6 py-4 whitespace-nowrap">{{ $org->contact_email }}</td>
						<td class="px-6 py-4 whitespace-nowrap">{{ $org->location }}</td>
						<td class="px-6 py-4 whitespace-nowrap">{{ $org->industry }}</td>
						<td class="px-6 py-4 whitespace-nowrap">{{ $org->opportunities->count() }}</td>
						<td class="px-6 py-4 whitespace-nowrap">{{ $org->applications->count() }}</td>
						<td class="px-6 py-4 whitespace-nowrap flex gap-2">
							<a href="{{ route('admin.organizations.show', $org->id) }}" class="text-indigo-600 hover:underline">View</a>
							<a href="{{ route('admin.organizations.edit', $org->id) }}" class="text-blue-600 hover:underline">Edit</a>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="6" class="px-6 py-4 text-center text-gray-500">No organizations found.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
			<!-- Pagination links -->
			<div class="mt-6 flex justify-center">{{ $organizations->links() }}</div>
		</div>

		<!-- Live filter JS (submits form on input change) -->
		<script>
			document.getElementById('orgSearchInput').addEventListener('input', function() {
				clearTimeout(window.orgFilterTimeout);
				window.orgFilterTimeout = setTimeout(function() {
					document.getElementById('orgFilterForm').submit();
				}, 400);
			});
		</script>
</div>
@endsection
