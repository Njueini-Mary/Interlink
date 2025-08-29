@extends('layouts.dashboard')
@section('content')
<h2>Activity Details</h2>
<ul>
<li>User: {{ $activity->user->name ?? '-' }}</li>
<li>Action: {{ $activity->action }}</li>
<li>Target: {{ $activity->target_type }}: {{ $activity->target_id }}</li>
<li>Created: {{ $activity->created_at }}</li>
</ul>
<a href="{{ route('admin.activities.edit', $activity->id) }}">Edit</a>
@endsection
