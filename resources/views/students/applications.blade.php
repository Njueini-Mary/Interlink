{{--
    Student Applications View
    Shows all applications submitted by the logged-in student.
    Controller: StudentController@applications
--}}
@extends('layouts.dashboard')
@section('title', 'My Applications')
@section('content')
<div class="max-w-7xl mx-auto py-10 px-6 bg-gray-50 rounded-lg shadow-lg">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-800 text-center">My Applications</h1>

    <div class="mb-6 flex justify-end">
        <form method="POST" action="{{ route('student.applications.calculate_matches') }}" onsubmit="showMLLoader()">
            @csrf
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 font-semibold">Calculate ML Matches</button>
        </form>
    </div>
    <x-ml-loader />
    @if($applications->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($applications as $application)
                @php
                    $studentSkills = $application->student->skills->pluck('name')->toArray() ?? [];
                    $opportunitySkills = $application->opportunity->skills->pluck('name')->toArray() ?? [];
                    $matchedSkills = is_array($application->matched_skills) ? $application->matched_skills : (json_decode($application->matched_skills, true) ?? []);
                    $missingSkills = is_array($application->missing_skills) ? $application->missing_skills : (json_decode($application->missing_skills, true) ?? []);
                    $tfidf = $application->tfidf_score ?? ($application->ml_scores['tfidf'] ?? 0);
                @endphp
                <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col justify-between border border-gray-200 hover:shadow-2xl transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-indigo-100 rounded-full p-3 mr-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $application->opportunity->title ?? 'N/A' }}</h2>
                    </div>
                    <div class="mb-2">
                        <span class="font-bold {{ $tfidf >= 80 ? 'text-green-600' : ($tfidf >= 60 ? 'text-blue-600' : ($tfidf >= 40 ? 'text-yellow-600' : 'text-red-600')) }}">
                            {{ round($tfidf) }}% Match
                        </span>
                        <span class="text-xs text-gray-500 ml-2">
                            @if($tfidf >= 80) Excellent Match
                            @elseif($tfidf >= 60) Good Match
                            @elseif($tfidf >= 40) Moderate Match
                            @else Low Match
                            @endif
                        </span>
                    </div>
                    <div class="mb-2 text-sm text-gray-700 bg-gray-50 rounded p-2">
                        {{ $application->match_message ? __(str_replace(['Fair match.', 'Low match.', 'Excellent match.', 'Good match.', 'Moderate match.'], ['You are a good fit for this opportunity.', 'You may need to improve your skills for this opportunity.', 'You are an excellent fit for this opportunity!', 'You are a strong candidate for this opportunity.', 'You are a decent fit for this opportunity.'], $application->match_message)) : 'This opportunity may be a good fit for you.' }}
                    </div>
                    <p class="text-gray-600 mb-2">Submitted on: <span class="font-medium">{{ $application->created_at->format('M d, Y') }}</span></p>
                    <span class="inline-block px-3 py-1 rounded-full bg-green-50 text-green-700 text-sm font-semibold mb-4">{{ ucfirst($application->status) }}</span>
                    <div class="flex justify-between items-center mb-2">
                        <a href="{{ route('opportunities.show', $application->opportunity_id) }}" class="text-blue-600 hover:underline font-medium">View Opportunity</a>
                        <span class="text-sm text-gray-500">{{ $application->organization->name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="font-bold text-gray-700">Your Skills:</span>
                        <div class="flex flex-wrap mt-1">
                            @foreach($studentSkills as $skill)
                                @if(in_array($skill, $matchedSkills))
                                    <span class="inline-block bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded mr-1 mb-1 font-semibold shadow-sm">{{ $skill }}</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-600 border border-gray-200 px-2 py-1 rounded mr-1 mb-1">{{ $skill }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-2">
                        <span class="font-bold text-gray-700">Required Skills:</span>
                        <div class="flex flex-wrap mt-1">
                            @foreach($opportunitySkills as $skill)
                                @if(in_array($skill, $matchedSkills))
                                    <span class="inline-block bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded mr-1 mb-1 font-semibold shadow-sm">{{ $skill }}</span>
                                @elseif(in_array($skill, $missingSkills))
                                    <span class="inline-block bg-red-50 text-red-700 border border-red-200 px-2 py-1 rounded mr-1 mb-1 font-bold shadow-sm animate-pulse">{{ $skill }}</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-600 border border-gray-200 px-2 py-1 rounded mr-1 mb-1">{{ $skill }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @if(isset($application->ml_scores))
                        <div class="mb-2">
                            <span class="font-bold text-gray-700">ML Scores:</span>
                            <span class="text-sm text-gray-600">TF-IDF: {{ $application->ml_scores['tfidf'] ?? 'N/A' }}%, Logistic Regression: {{ $application->ml_scores['logreg'] ?? 'N/A' }}%, Random Forest: {{ $application->ml_scores['rf'] ?? 'N/A' }}%</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $applications->links('pagination::tailwind') }}
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg flex items-center">
            <svg class="w-8 h-8 text-yellow-400 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01" />
            </svg>
            <span class="text-gray-700">You have not submitted any applications yet.</span>
        </div>
    @endif
</div>
@endsection
