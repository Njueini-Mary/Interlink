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
                                <a href="#" class="text-blue-600 hover:underline font-medium" data-bs-toggle="modal" data-bs-target="#applicantModal" data-applicant="{{ json_encode($application->student) }}">
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
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="applicantModal" tabindex="-1" aria-labelledby="applicantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicantModalLabel">Applicant Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="applicantDetails">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const applicantModal = document.getElementById('applicantModal');
        applicantModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const applicantData = button.getAttribute('data-applicant');

            // Debugging: Log the button and data-applicant attribute
            console.log('Button clicked:', button);
            console.log('Applicant Data (raw):', applicantData);

            // Ensure the data-applicant attribute is valid JSON
            try {
                const applicant = JSON.parse(applicantData);

                // Debugging: Log the parsed applicant object
                console.log('Parsed Applicant Object:', applicant);

                const detailsContainer = document.getElementById('applicantDetails');
                detailsContainer.innerHTML = `
                    <p><strong>Name:</strong> ${applicant.user.name}</p>
                    <p><strong>Email:</strong> ${applicant.user.email}</p>
                    <p><strong>University:</strong> ${applicant.university || 'N/A'}</p>
                    <p><strong>Course:</strong> ${applicant.course || 'N/A'}</p>
                    <p><strong>Year:</strong> ${applicant.year || 'N/A'}</p>
                    <p><strong>Resume:</strong> <a href="/${applicant.resume}" target="_blank">View Resume</a></p>
                `;
            } catch (error) {
                console.error('Error parsing applicant data:', error);
                alert('Failed to load applicant details. Please check the console for more information.');
            }
        });
    });
</script>
@endsection
