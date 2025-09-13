
@extends('layouts.dashboard')
@section('content')

<div class="max-w-4xl mx-auto py-10">
	<h2 class="text-3xl font-bold mb-8 text-indigo-700">Edit Organization</h2>

	@if ($errors->any())
		<div class="mb-6">
			<div class="bg-red-50 border border-red-200 rounded-lg p-4">
				<ul class="list-disc list-inside text-red-600">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		</div>
	@endif

	<!-- Organization Details Card -->
	<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
		<div class="bg-gray-50 rounded-lg p-6 shadow">
			<h3 class="text-xl font-semibold mb-4 text-gray-800">Basic Info</h3>
			<form method="POST" action="{{ route('admin.organizations.update', $organization->id) }}" class="space-y-6">
				@csrf
				@method('PUT')
				<div>
					<label for="name" class="block text-sm font-medium text-gray-700">Name</label>
					<input type="text" name="name" id="name" value="{{ old('name', $organization->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
				</div>
				<div>
					<label for="industry" class="block text-sm font-medium text-gray-700">Industry</label>
					<input type="text" name="industry" id="industry" value="{{ old('industry', $organization->industry) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
				</div>
				<div>
					<label for="location" class="block text-sm font-medium text-gray-700">Location</label>
					<input type="text" name="location" id="location" value="{{ old('location', $organization->location) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
				</div>
				<div>
					<label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
					<input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $organization->contact_email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
				</div>
				<div>
					<label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
					<input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $organization->contact_phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
				</div>
				<div>
					<label for="website" class="block text-sm font-medium text-gray-700">Website</label>
					<input type="url" name="website" id="website" value="{{ old('website', $organization->website) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
				</div>
				<div>
					<label for="profile" class="block text-sm font-medium text-gray-700">Profile</label>
					<textarea name="profile" id="profile" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('profile', $organization->profile) }}</textarea>
				</div>
				<div class="flex justify-end">
					<button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Update Organization</button>
				</div>
			</form>
		</div>

		<!-- Metrics & Related Data -->
		<div class="bg-white rounded-lg p-6 shadow">
			<h3 class="text-xl font-semibold mb-4 text-gray-800">Metrics & Related Data</h3>
			<div class="grid grid-cols-2 gap-4 mb-6">
				<div class="bg-green-50 rounded-lg px-4 py-2 text-center">
					<span class="block text-lg font-bold text-green-700">{{ $organization->opportunities->count() }}</span>
					<span class="text-xs text-gray-500">Jobs/Opportunities</span>
				</div>
				<div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
					<span class="block text-lg font-bold text-blue-700">{{ $organization->students->count() }}</span>
					<span class="text-xs text-gray-500">Interns/Students</span>
				</div>
			</div>
			<div class="mb-4">
				<h4 class="font-semibold text-indigo-700 mb-2">Jobs & Opportunities</h4>
				<ul class="space-y-1">
					@forelse($organization->opportunities as $opp)
						<li class="bg-indigo-50 rounded px-2 py-1 text-sm">
							<span class="font-semibold">{{ $opp->title }}</span>
							<span class="text-xs text-gray-500">{{ $opp->location }} | {{ ucfirst($opp->type) }} | Status: {{ ucfirst($opp->status) }}</span>
						</li>
					@empty
						<li class="text-gray-400">No jobs/opportunities found.</li>
					@endforelse
				</ul>
			</div>
			<div class="mb-4">
				<h4 class="font-semibold text-blue-700 mb-2">Interns & Students</h4>
				<ul class="space-y-1">
					@forelse($organization->students as $student)
						<li class="bg-blue-50 rounded px-2 py-1 text-sm">
							<span class="font-semibold">{{ $student->university }} - {{ $student->course }}</span>
							<span class="text-xs text-gray-500">Year: {{ $student->year }} | Field: {{ $student->academic_field }}</span>
						</li>
					@empty
						<li class="text-gray-400">No interns/students found.</li>
					@endforelse
				</ul>
			</div>
			<div class="mb-4">
				<h4 class="font-semibold text-pink-700 mb-2">Applications</h4>
				<ul class="space-y-1">
					@forelse($organization->applications as $app)
						<li class="bg-pink-50 rounded px-2 py-1 text-sm">
							<span class="font-semibold">{{ $app->student->university ?? 'Student' }} - {{ $app->student->course ?? '' }}</span>
							<span class="text-xs text-gray-500">Status: {{ ucfirst($app->status) }}</span>
						</li>
					@empty
						<li class="text-gray-400">No applications found.</li>
					@endforelse
				</ul>
			</div>
			<div>
				<h4 class="font-semibold text-gray-700 mb-2">Staff & Admins</h4>
				<ul class="space-y-1">
					@forelse($organization->users as $user)
						<li class="bg-gray-50 rounded px-2 py-1 text-sm">
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
	</div>
</div>
@endsection
