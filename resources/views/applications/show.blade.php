@extends('layouts.dashboard')
@section('content')
<div class="max-w-3xl mx-auto py-10 px-6 bg-gray-50 rounded-lg shadow-lg">
	<h2 class="text-3xl font-extrabold mb-8 text-gray-800 text-center">Application Details</h2>
	<div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
		<div class="bg-white rounded-lg shadow p-6">
			<h3 class="text-xl font-bold text-indigo-700 mb-2">Opportunity</h3>
			<p class="text-gray-800 mb-2">{{ $application->opportunity->title ?? '-' }}</p>
			<p class="text-gray-600 text-sm">Status: <span class="font-semibold">{{ ucfirst($application->status) }}</span></p>
			<p class="text-gray-600 text-sm">Submitted: <span class="font-semibold">{{ $application->created_at->format('M d, Y') }}</span></p>
		</div>
		<div class="bg-white rounded-lg shadow p-6">
			<h3 class="text-xl font-bold text-indigo-700 mb-2">Student</h3>
			<p class="text-gray-800 mb-2">{{ $application->student->user->name ?? 'N/A' }}</p>
			<p class="text-gray-600 text-sm">Email: <span class="font-semibold">{{ $application->student->user->email ?? 'N/A' }}</span></p>
			<p class="text-gray-600 text-sm">Course: <span class="font-semibold">{{ $application->student->course ?? 'N/A' }}</span></p>
			<p class="text-gray-600 text-sm">University: <span class="font-semibold">{{ $application->student->university ?? 'N/A' }}</span></p>
		</div>
	</div>
	<div class="mb-8">
		@include('components.ml-loader')
		@if(auth()->user() && auth()->user()->role === 'student' && $application->student_id === auth()->user()->student->id)
			<div class="mb-4 text-center">
				<form method="POST" action="{{ route('student.applications.calculate_match', $application->id) }}" onsubmit="showMLLoader()">
					@csrf
					<button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 font-semibold">Recalculate ML Match</button>
				</form>
			</div>
		@endif
		<div class="mb-4 text-center">
			<form method="POST" action="{{ route('applications.calculate_match', $application->id) }}" onsubmit="showMLLoader()">
				@csrf
				<button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 font-semibold">
					<i class="fas fa-robot mr-2"></i> Recalculate ML Match
				</button>
			</form>
		</div>

		<h3 class="text-xl font-bold text-indigo-700 mb-4">ML Match Metrics</h3>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<div class="bg-indigo-50 rounded-lg p-4 text-center shadow">
				<div class="text-xs text-gray-500 mb-1">Random Forest</div>
				<div class="text-2xl font-bold text-indigo-700">{{ round($application->rf_score ?? $application->match_score ?? 0) }}%</div>
			</div>
			<div class="bg-blue-50 rounded-lg p-4 text-center shadow">
				<div class="text-xs text-gray-500 mb-1">TF-IDF</div>
				<div class="text-2xl font-bold text-blue-700">{{ round($application->tfidf_score ?? 0) }}%</div>
			</div>
			<div class="bg-green-50 rounded-lg p-4 text-center shadow">
				<div class="text-xs text-gray-500 mb-1">Logistic Regression</div>
				<div class="text-2xl font-bold text-green-700">{{ round($application->logreg_score ?? 0) }}%</div>
			</div>
		</div>
		<div class="mt-4 text-xs text-gray-700">
			<strong>Applicant Skills:</strong> {{ implode(', ', $application->student->skills->pluck('name')->toArray() ?? []) }}<br>
			<strong>Required Skills:</strong> {{ implode(', ', $application->opportunity->skills->pluck('name')->toArray() ?? []) }}
		</div>
		{{-- XAI Section --}}
		@php
			$matchedSkills = is_string($application->matched_skills) ? json_decode($application->matched_skills, true) : $application->matched_skills;
			$missingSkills = is_string($application->missing_skills) ? json_decode($application->missing_skills, true) : $application->missing_skills;
		@endphp
		@if(isset($application->match_message) || (is_array($matchedSkills) && count($matchedSkills)) || (is_array($missingSkills) && count($missingSkills)))
		<div class="mt-6 p-4 bg-gray-50 rounded">
			<div class="text-xs text-gray-700 mb-1 font-semibold">Explainable AI (XAI):</div>
			@if(isset($application->match_message))
				<div class="text-xs text-indigo-700 mb-1">{{ $application->match_message }}</div>
			@endif
			@if(is_array($matchedSkills) && count($matchedSkills))
				<div class="text-xs text-green-700 mb-1">Matched Skills: <span class="font-semibold">{{ implode(', ', $matchedSkills) }}</span></div>
			@endif
			@if(is_array($missingSkills) && count($missingSkills))
				<div class="text-xs text-red-700 mb-1">Missing Skills: <span class="font-semibold">{{ implode(', ', $missingSkills) }}</span></div>
			@endif
			<div class="mt-2">
				<span class="text-xs text-blue-700 mr-2">Course: <span class="font-semibold">{{ $application->student->course ?? 'N/A' }}</span></span>
				<span class="text-xs text-blue-700 mr-2">Academic Field: <span class="font-semibold">{{ $application->student->academic_field ?? 'N/A' }}</span></span>
				<span class="text-xs text-purple-700 mr-2">GPA: <span class="font-semibold">{{ $application->student->gpa ?? 'N/A' }}</span></span>
				<span class="text-xs text-orange-700">Interests: <span class="font-semibold">{{ $application->student->interests ?? 'N/A' }}</span></span>
			</div>
		</div>
		@endif
	</div>
</div>
@endsection
