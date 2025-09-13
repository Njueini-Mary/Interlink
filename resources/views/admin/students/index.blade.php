
@extends('layouts.dashboard')
@section('content')

<!-- Students Metrics -->
<div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
	<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-blue-700">{{ $students->count() }}</span>
		<span class="text-xs text-gray-500">Total Students</span>
	</div>
	<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-green-700">{{ $students->where('year', '>=', now()->year)->count() }}</span>
		<span class="text-xs text-gray-500">Current Year</span>
	</div>
	<div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-indigo-700">{{ $students->unique('university')->count() }}</span>
		<span class="text-xs text-gray-500">Universities</span>
	</div>
	<div class="bg-pink-50 rounded-lg px-4 py-2 text-center">
		<span class="block text-lg font-bold text-pink-700">{{ $students->unique('course')->count() }}</span>
		<span class="text-xs text-gray-500">Courses</span>
	</div>
</div>


<!-- Live Search & Filter -->
<div class="mb-6 flex flex-col md:flex-row md:items-center gap-4">
	<input id="student-search" type="text" placeholder="Search by name, university, or course..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" />
	<select id="student-year" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
		<option value="">All Years</option>
		@foreach($students->pluck('year')->unique() as $year)
			<option value="{{ $year }}">{{ $year }}</option>
		@endforeach
	</select>
	<select id="student-university" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
		<option value="">All Universities</option>
		@foreach($students->pluck('university')->unique() as $uni)
			<option value="{{ $uni }}">{{ $uni }}</option>
		@endforeach
	</select>
</div>

<script>
// Simple live filter for demo (client-side)
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('student-search');
	const yearSelect = document.getElementById('student-year');
	const uniSelect = document.getElementById('student-university');
	const rows = document.querySelectorAll('tbody tr');
	function filterRows() {
		const search = searchInput.value.toLowerCase();
		const year = yearSelect.value;
		const uni = uniSelect.value;
		rows.forEach(row => {
			const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
			const university = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
			const course = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
			const rowYear = row.querySelector('td:nth-child(5)').textContent;
			let show = true;
			if (search && !(name.includes(search) || university.includes(search) || course.includes(search))) show = false;
			if (year && rowYear !== year) show = false;
			if (uni && university !== uni.toLowerCase()) show = false;
			row.style.display = show ? '' : 'none';
		});
	}
	searchInput.addEventListener('input', filterRows);
	yearSelect.addEventListener('change', filterRows);
	uniSelect.addEventListener('change', filterRows);
});
</script>

<!-- Students Table - Modern Card Layout -->
<div class="bg-white rounded-lg shadow p-6">
	<h2 class="text-2xl font-bold mb-4 text-indigo-700">All Students</h2>
	<table class="table-auto w-full">
		<thead>
			<tr class="bg-gray-100">
				<th class="px-4 py-2 text-left">#</th>
				<th class="px-4 py-2 text-left">Name</th>
				<th class="px-4 py-2 text-left">University</th>
				<th class="px-4 py-2 text-left">Course</th>
				<th class="px-4 py-2 text-left">Year</th>
				<th class="px-4 py-2 text-left">Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($students as $i => $student)
			<tr class="border-b">
				<td class="px-4 py-2">{{ ($students->currentPage() - 1) * $students->perPage() + $i + 1 }}</td>
				<td class="px-4 py-2">{{ $student->user->name ?? '-' }}</td>
				<td class="px-4 py-2">{{ $student->university }}</td>
				<td class="px-4 py-2">{{ $student->course }}</td>
				<td class="px-4 py-2">{{ $student->year }}</td>
				<td class="px-4 py-2">
					<a href="{{ route('admin.students.show', $student->id) }}" class="text-indigo-600 hover:underline">View</a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<!-- Pagination -->
	<div class="mt-6 flex justify-center">
		{{ $students->links() }}
	</div>
</div>

{{-- End of Students Index View --}}
@endsection
