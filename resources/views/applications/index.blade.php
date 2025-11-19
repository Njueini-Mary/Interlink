@extends('layouts.dashboard')
@section('content')
<div class="max-w-7xl mx-auto py-10 px-6 bg-gray-50 rounded-lg shadow-lg">
    <h2 class="text-3xl font-extrabold mb-8 text-gray-800 text-center">Applications</h2>
    @if(!empty($profileMissing) && $profileMissing)
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg mb-6">
            Your profile is incomplete. Please complete your organization profile to view applications.
        </div>
    @elseif($applications->isEmpty())
        <div class="bg-gray-100 text-gray-700 p-4 rounded-lg mb-6">
            No applications found.
        </div>
    @else
        <div class="mb-6 flex gap-4">
            <a href="{{ route('applications.export', ['type' => 'excel']) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Export Excel</a>
            <a href="{{ route('applications.export', ['type' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Export PDF</a>
        </div>
        <div x-data="{ showModal: false, applicant: null }">
            <div class="overflow-x-auto bg-white rounded-2xl shadow-lg ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-blue-50 to-blue-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Applicant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Opportunity</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ML Match</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Applicant Skills</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Requirements</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Resume</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($applications as $application)
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
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 flex items-center gap-3">
                                <button class="flex items-center gap-2 text-blue-700 hover:underline font-medium" @click="showModal = true; applicant = {{ json_encode($application->student) }}">
                                        <span class="inline-block w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                                            {{ strtoupper(substr($application->student->user->name ?? $application->student->full_name ?? $application->student->name ?? '', 0, 1)) }}
                                        </span>
                                        <span>{{ $application->student->user->name ?? $application->student->full_name ?? $application->student->name ?? '' }}</span>
                                    </button>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-gray-700">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L15 12.75 9.75 8.5" /></svg>
                                    {{ $application->opportunity->title ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{
                                    $application->status === 'accepted' ? 'bg-green-100 text-green-700' :
                                    ($application->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')
                                }}">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        @if($application->status === 'accepted')
                                            <circle cx="10" cy="10" r="10" fill="#34d399" />
                                        @elseif($application->status === 'rejected')
                                            <circle cx="10" cy="10" r="10" fill="#f87171" />
                                        @else
                                            <circle cx="10" cy="10" r="10" fill="#a1a1aa" />
                                        @endif
                                    </svg>
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $application->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 align-middle">
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
                                        <strong>Match Message:</strong> {{ $matchMessage }}
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                @foreach($studentSkills as $skill)
                                    @if(in_array($skill, $matchedSkills))
                                        <span class="inline-block bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded mr-1 mb-1 font-semibold shadow-sm">{{ $skill }}</span>
                                    @else
                                        <span class="inline-block bg-gray-100 text-gray-600 border border-gray-200 px-2 py-1 rounded mr-1 mb-1">{{ $skill }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-4 py-3 align-middle">
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
                            <td class="px-4 py-3 align-middle">
                                @if($application->resume)
                                    <a href="{{ asset('storage/' . $application->resume) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold">
                                        <i class="fas fa-file-pdf mr-1"></i> <span class="sr-only">Resume PDF</span>
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 focus:outline-none flex items-center gap-1">
                                        <span>Actions</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-44 bg-white border border-gray-100 rounded-lg shadow-lg z-20">
                                        <form method="POST" action="{{ route('applications.approve', $application->id) }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-blue-50">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('applications.reject', $application->id) }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-blue-50">Reject</button>
                                        </form>
                                        <a href="{{ route('applications.show', $application->id) }}" class="block px-4 py-2 hover:bg-blue-50">View</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Modal -->
            <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60 backdrop-blur-sm" style="display: none;">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative animate-fade-in">
                    <button @click="showModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <template x-if="applicant">
                        <div>
                            <div class="flex items-center gap-4 mb-6">
                                <span class="inline-block w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-2xl font-bold">
                                    <span x-text="(applicant.user && applicant.user.name ? applicant.user.name.charAt(0) : (applicant.full_name ? applicant.full_name.charAt(0) : (applicant.name ? applicant.name.charAt(0) : ''))) .toUpperCase()"></span>
                                </span>
                                <div>
                                    <h3 class="text-2xl font-bold" x-text="applicant.user && applicant.user.name ? applicant.user.name : (applicant.full_name ? applicant.full_name : (applicant.name ? applicant.name : ''))"></h3>
                                    <div class="text-gray-500 text-sm mt-1" x-text="applicant.user && applicant.user.email ? applicant.user.email : (applicant.email ? applicant.email : '')"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3">
                                <div><span class="font-semibold text-gray-700">Phone:</span> <span class="text-gray-600" x-text="applicant.phone || 'N/A'"></span></div>
                                <div><span class="font-semibold text-gray-700">Skills:</span> <span class="text-gray-600" x-text="Array.isArray(applicant.skills) ? applicant.skills.map(s => s.name).join(', ') : (applicant.skills || 'N/A')"></span></div>
                                <div><span class="font-semibold text-gray-700">Education:</span> <span class="text-gray-600" x-text="applicant.education || 'N/A'"></span></div>
                                <div><span class="font-semibold text-gray-700">Bio:</span> <span class="text-gray-600" x-text="applicant.bio || 'N/A'"></span></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <!-- End Modal -->
        </div>
    @endif
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
