@extends('layouts.dashboard')
@section('content')
<h2>All Applications</h2>
<table class="table-auto w-full">
<thead><tr><th>Student</th><th>Opportunity</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@foreach($applications as $application)
<tr>
<td>{{ $application->student->user->name ?? '-' }}</td>
<td>{{ $application->opportunity->title ?? '-' }}</td>
<td>{{ $application->status }}</td>
<td><a href="{{ route('admin.applications.show', $application->id) }}">View</a></td>
</tr>
@endforeach
</tbody>
</table>
@endsection
