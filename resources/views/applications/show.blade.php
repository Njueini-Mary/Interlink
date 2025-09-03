@extends('layouts.dashboard')
@section('content')
<h2>Application Details</h2>
<ul>
<li>Opportunity: {{ $application->opportunity->title ?? '-' }}</li>
<li>Status: {{ $application->status }}</li>
<li>Submitted: {{ $application->created_at }}</li>
@if(isset($application->student->user))
<li>Student: {{ $application->student->user->name }}</li>
@endif
</ul>
@endsection
