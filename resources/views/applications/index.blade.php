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
        <div x-data="{ showModal: false, applicant: null }">
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-200 rounded-lg shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-600 font-semibold">Applicant</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-semibold">Opportunity</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-semibold">Status</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-semibold">Submitted</th>
                            <th class="px-4 py-2 text-left text-gray-600 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-800">
                                    <a href="#" class="text-blue-600 hover:underline font-medium"
                                        @click.prevent="showModal = true; applicant = {
                                            ...{{ json_encode($application->student) }},
                                            resume: '{{ $application->resume }}',
                                            interests: '{{ $application->interests }}',
                                            gpa: '{{ $application->gpa }}',
                                            cover_letter: '{{ $application->cover_letter ?? '' }}'
                                        }">
                                        {{ $application->student->user->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="px-4 py-2 text-gray-800">{{ $application->opportunity->title ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ ucfirst($application->status) }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $application->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2">
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
