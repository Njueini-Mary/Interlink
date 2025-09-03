@extends('layouts.dashboard')
@section('content')
<h2>My Applications</h2>
@if(!empty($profileMissing) && $profileMissing)
	<div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4">
		Your profile is incomplete. Please complete your student or organization profile to view applications.
	</div>
@elseif($applications->isEmpty())
	<div class="bg-gray-100 text-gray-700 p-4 rounded mb-4">
		No applications found.
	</div>
@else
<table class="table-auto w-full">
<thead><tr><th>Opportunity</th><th>Status</th><th>Submitted</th><th>Actions</th></tr></thead>
<tbody>
@foreach($applications as $application)
<tr>
<td>{{ $application->opportunity->title ?? '-' }}</td>
<td>{{ $application->status }}</td>
<td>{{ $application->created_at }}</td>
<td><a href="{{ route('applications.show', $application->id) }}">View</a></td>
</tr>
@endforeach
</tbody>
</table>
@endif
@endsection
