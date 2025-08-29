@extends('layouts.dashboard')
@section('content')
<h2>Application Details</h2>
<ul>
<li>Student: {{ $application->student->user->name ?? '-' }}</li>
<li>Opportunity: {{ $application->opportunity->title ?? '-' }}</li>
<li>Status: {{ $application->status }}</li>
<li>Submitted: {{ $application->created_at }}</li>
</ul>
<a href="{{ route('admin.applications.edit', $application->id) }}">Edit</a>
@endsection
