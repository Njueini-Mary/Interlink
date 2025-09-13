
@extends('layouts.dashboard')
@section('content')
<div class="max-w-2xl mx-auto py-10">
	<!-- Organization Card -->
	<div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col items-center">
		<!-- Logo -->
		@if($organization->logo)
			<img src="{{ asset('storage/' . $organization->logo) }}" alt="Logo" class="w-24 h-24 rounded-full object-cover mb-4 border-2 border-indigo-200">
		@else
			<div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mb-4 text-gray-400 text-4xl">
				<i class="fas fa-building"></i>
			</div>
		@endif
		<!-- Name & Industry -->
		<h2 class="text-3xl font-bold mb-2 text-gray-800 text-center">{{ $organization->name }}</h2>
		<span class="inline-block bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold mb-4">{{ $organization->industry ?? 'Industry N/A' }}</span>


			<!-- Metrics & Related Data -->
			<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 w-full">
				<div class="bg-gray-50 rounded-lg px-4 py-2 text-center">
					<span class="block text-lg font-bold text-gray-700">{{ $organization->location }}</span>
					<span class="text-xs text-gray-500">Location</span>
				</div>
				<div class="bg-gray-50 rounded-lg px-4 py-2 text-center">
					<span class="block text-lg font-bold text-gray-700">{{ $organization->contact_email }}</span>
					<span class="text-xs text-gray-500">Email</span>
				</div>
				<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
					<span class="block text-lg font-bold text-green-700">{{ $organization->opportunities->count() }}</span>
					<span class="text-xs text-gray-500">Jobs/Opportunities</span>
				</div>
				<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
					<span class="block text-lg font-bold text-blue-700">{{ $organization->students->count() }}</span>
					<span class="text-xs text-gray-500">Interns/Students</span>
				</div>
			</div>

			<!-- Details -->
			<div class="w-full mb-6">
				@if($organization->website)
					<p class="mb-2 text-gray-600"><span class="font-semibold">Website:</span> <a href="{{ $organization->website }}" class="text-indigo-600 hover:underline" target="_blank">{{ $organization->website }}</a></p>
				@endif
				@if($organization->contact_phone)
					<p class="mb-2 text-gray-600"><span class="font-semibold">Phone:</span> {{ $organization->contact_phone }}</p>
				@endif
				@if($organization->profile)
					<p class="mt-4 text-gray-700"><span class="font-semibold">Profile:</span> {{ $organization->profile }}</p>
				@endif
			</div>

			<!-- Related Data Lists -->
			<div class="w-full grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
				<!-- Jobs/Opportunities -->
				<div>
					<h3 class="text-lg font-bold mb-2 text-indigo-700">Jobs & Opportunities</h3>
					<ul class="space-y-2">
						@forelse($organization->opportunities as $opp)
							<li class="bg-indigo-50 rounded px-3 py-2 flex flex-col">
								<span class="font-semibold">{{ $opp->title }}</span>
								<span class="text-xs text-gray-500">{{ $opp->location }} | {{ ucfirst($opp->type) }} | Status: {{ ucfirst($opp->status) }}</span>
								<span class="text-xs text-gray-400">Deadline: {{ $opp->deadline }}</span>
							</li>
						@empty
							<li class="text-gray-400">No jobs/opportunities found.</li>
						@endforelse
					</ul>
				</div>
				<!-- Interns/Students -->
				<div>
					<h3 class="text-lg font-bold mb-2 text-blue-700">Interns & Students</h3>
					<ul class="space-y-2">
						@forelse($organization->students as $student)
							<li class="bg-blue-50 rounded px-3 py-2 flex flex-col">
								<span class="font-semibold">{{ $student->university }} - {{ $student->course }}</span>
								<span class="text-xs text-gray-500">Year: {{ $student->year }} | Field: {{ $student->academic_field }}</span>
							</li>
						@empty
							<li class="text-gray-400">No interns/students found.</li>
						@endforelse
					</ul>
				</div>
			</div>

			<!-- Applications & Staff (optional, show if exists) -->
			<div class="w-full grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
				<div>
					<h3 class="text-lg font-bold mb-2 text-pink-700">Applications</h3>
					<ul class="space-y-2">
						@forelse($organization->applications as $app)
							<li class="bg-pink-50 rounded px-3 py-2 flex flex-col">
								<span class="font-semibold">{{ $app->student->university ?? 'Student' }} - {{ $app->student->course ?? '' }}</span>
								<span class="text-xs text-gray-500">Status: {{ ucfirst($app->status) }}</span>
							</li>
						@empty
							<li class="text-gray-400">No applications found.</li>
						@endforelse
					</ul>
				</div>
				<div>
					<h3 class="text-lg font-bold mb-2 text-gray-700">Staff & Admins</h3>
					<ul class="space-y-2">
						@forelse($organization->users as $user)
							<li class="bg-gray-50 rounded px-3 py-2 flex flex-col">
								<span class="font-semibold">{{ $user->name }}</span>
								<span class="text-xs text-gray-500">Role: {{ $user->pivot->role ?? 'Staff' }}</span>
								<span class="text-xs text-gray-400">Email: {{ $user->email }}</span>
							</li>
						@empty
							<li class="text-gray-400">No staff/admins found.</li>
						@endforelse
					</ul>
				</div>
			</div>

		<!-- Action Buttons -->
		<div class="flex gap-4 mt-4">
			<a href="{{ route('admin.organizations.edit', $organization->id) }}" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700">Edit</a>
			<form method="POST" action="{{ route('admin.organizations.delete', $organization->id) }}" onsubmit="return confirm('Are you sure you want to delete this organization?');">
				@csrf
				@method('DELETE')
				<button type="submit" class="bg-red-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-red-700">Delete</button>
			</form>
			<a href="{{ route('admin.organizations.index') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg font-semibold hover:bg-gray-300">Back</a>
		</div>
	</div>
</div>
@endsection
