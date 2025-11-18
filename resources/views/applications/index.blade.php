@extends('layouts.dashboard')
@section('content')
<div class="max-w-7xl mx-auto py-10 px-6 bg-gray-50 rounded-lg shadow-lg">
    <h2 class="text-3xl font-extrabold mb-8 text-gray-800 text-center">Applications for My Jobs</h2>

    @if(!empty($profileMissing) && $profileMissing)
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg mb-6">
            Your profile is incomplete. Please complete your organization profile to view applications.
        </div>
    @elseif($applications->isEmpty())
        <div class="bg-gray-100 text-gray-700 p-4 rounded-lg mb-6">
            No applications found for your posted jobs.
        </div>
    @else
        <!-- Export Buttons -->
        <div class="mb-6 flex gap-4">
            <a href="{{ route('applications.export', ['type' => 'excel']) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Export Excel</a>
            <a href="{{ route('applications.export', ['type' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Export PDF</a>
        </div>
        <div x-data="{ showModal: false, applicant: null }">
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-200 rounded-lg shadow-md">
                    <thead class="bg-gray-100">
                        <tr class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider">
                            <th class="px-4 py-3 rounded-tl-lg">#</th>
                            <th class="px-4 py-3">Applicant</th>
                            <th class="px-4 py-3">Opportunity</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Submitted</th>
                            <th class="px-4 py-3">ML Match (TFIDF)</th>
                            <th class="px-4 py-3">Applicant Skills</th>
                            <th class="px-4 py-3 rounded-tr-lg">Requirements</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            <tr class="hover:bg-indigo-50 transition-colors">
                                <td class="border px-4 py-3 text-center font-semibold text-gray-500">{{ $loop->iteration }}</td>
                                <td class="border px-4 py-3 font-medium text-gray-900">
                                    <a href="#" class="text-blue-600 hover:underline font-medium"
                                        @click.prevent="showModal = true; applicant = {
                                            ...{{ json_encode($application->student) }},
                                            resume: '{{ $application->resume }}',
                                            interests: '{{ $application->interests }}',
                                            gpa: '{{ $application->gpa }}',
                                            cover_letter: '{{ $application->cover_letter ?? '' }}',
                                            student_skills: @json($application->student->skills->pluck('name')->toArray()),
                                            opportunity_skills: @json($application->opportunity->skills->pluck('name')->toArray()),
                                            tfidf_score: '{{ $application->tfidf_score ?? '' }}',
                                            logreg_score: '{{ $application->logreg_score ?? '' }}',
                                            rf_score: '{{ $application->rf_score ?? '' }}'
                                        }">
                                        {{ $application->student->user->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="border px-4 py-3">{{ $application->opportunity->title ?? '-' }}</td>
                                <td class="border px-4 py-3">
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $application->status === 'accepted' ? 'bg-green-100 text-green-700' : ($application->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="border px-4 py-3 text-gray-700">{{ $application->created_at->format('M d, Y') }}</td>
                                <td class="border px-4 py-3">
                                    @php
                                        $matcher = app(\App\Services\SkillMatcher::class);
                                        $studentSkills = $application->student && $application->student->skills ? $application->student->skills->pluck('name')->toArray() : [];
                                        $opportunitySkills = $application->opportunity && $application->opportunity->skills ? $application->opportunity->skills->pluck('name')->toArray() : [];
                                        $matchedSkills = $matcher->getMatchedSkills($opportunitySkills, $studentSkills);
                                        $missingSkills = $matcher->getMissingSkills($opportunitySkills, $studentSkills);
                                        $matchMessage = $matcher->getMatchMessage(
                                            $application->tfidf_score ?? 0,
                                            $matchedSkills,
                                            $missingSkills,
                                            $opportunitySkills,
                                            $application->student->course ?? null,
                                            $application->student->academic_field ?? null,
                                            $application->student->gpa ?? null,
                                            $application->student->interests ? (is_array($application->student->interests) ? $application->student->interests : explode(',', $application->student->interests)) : null,
                                            auth()->user()->role ?? null
                                        );
                                    @endphp
                                    @if(isset($application->tfidf_score))
                                        <span class="font-bold {{ $application->tfidf_score >= 80 ? 'text-green-600' : ($application->tfidf_score >= 60 ? 'text-blue-600' : ($application->tfidf_score >= 40 ? 'text-yellow-600' : 'text-red-600')) }}">
                                            {{ round($application->tfidf_score) }}%
                                        </span>
                                        <span class="text-xs text-gray-500 ml-2">
                                            @if($application->tfidf_score >= 80) Excellent Match
                                            @elseif($application->tfidf_score >= 60) Good Match
                                            @elseif($application->tfidf_score >= 40) Moderate Match
                                            @else Low Match
                                            @endif
                                        </span>
                                        <div class="mt-2 text-xs text-indigo-700 bg-indigo-50 rounded p-2">
                                            <strong>XAI:</strong> {{ $matchMessage }}
                                        </div>
                                    @endif
                                </td>
                                <td class="border px-4 py-3">
                                    @foreach($studentSkills as $skill)
                                        @if(in_array($skill, $matchedSkills))
                                            <span class="inline-block bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded mr-1 mb-1 font-semibold shadow-sm">{{ $skill }}</span>
                                        @else
                                            <span class="inline-block bg-gray-100 text-gray-600 border border-gray-200 px-2 py-1 rounded mr-1 mb-1">{{ $skill }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="border px-4 py-3">
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
                                <td class="border px-4 py-3">
                                    <a href="{{ route('applications.approve', $application->id) }}" class="text-green-600 hover:underline font-medium ml-4">Approve</a>
                                    <a href="{{ route('applications.reject', $application->id) }}" class="text-red-600 hover:underline font-medium ml-4">Reject</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modern Modal -->
            <div x-show="showModal" style="display: none;" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl p-8 relative">
                    <button @click="showModal = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                    <h3 class="text-2xl font-bold mb-6 text-center text-indigo-700">Applicant Profile</h3>
                    <template x-if="applicant">
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="font-semibold text-gray-600">Name:</p>
                                    <p class="text-gray-800" x-text="applicant.user ? applicant.user.name : 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Email:</p>
                                    <p class="text-gray-800" x-text="applicant.user ? applicant.user.email : 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">University:</p>
                                    <p class="text-gray-800" x-text="applicant.university || 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Course:</p>
                                    <p class="text-gray-800" x-text="applicant.course || 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Year:</p>
                                    <p class="text-gray-800" x-text="applicant.year || 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Current GPA:</p>
                                    <p class="text-gray-800" x-text="applicant.gpa || 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Interests:</p>
                                    <p class="text-gray-800" x-text="applicant.interests || 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Bio:</p>
                                    <p class="text-gray-800" x-text="applicant.bio || 'N/A'"></p>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-600">Cover Letter:</p>
                                <p class="text-gray-800 whitespace-pre-line" x-text="applicant.cover_letter || 'N/A'"></p>
                            </div>
                            <template x-if="applicant.resume">
                                <div>
                                    <p class="font-semibold text-gray-600">Resume:</p>
                                    <a :href="'/storage/' + applicant.resume" target="_blank" class="text-indigo-600 underline">View Resume</a>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div class="flex justify-center mt-8">
                        <button @click="showModal = false" class="px-6 py-2 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 font-semibold">Close</button>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
        </div>
    @endif
</div>
@endsection
