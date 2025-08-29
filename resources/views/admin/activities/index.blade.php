@extends('layouts.dashboard')
@section('content')
<h2>All Activities</h2>
<table class="table-auto w-full">
<thead><tr><th>User</th><th>Action</th><th>Target</th><th>Created</th><th>Actions</th></tr></thead>
<tbody>
@foreach($activities as $activity)
<tr>
<td>{{ $activity->user->name ?? '-' }}</td>
<td>{{ $activity->action }}</td>
<td>{{ $activity->target_type }}: {{ $activity->target_id }}</td>
<td>{{ $activity->created_at }}</td>
<td><a href="{{ route('admin.activities.show', $activity->id) }}">View</a></td>
</tr>
@endforeach
</tbody>
</table>
@endsection
