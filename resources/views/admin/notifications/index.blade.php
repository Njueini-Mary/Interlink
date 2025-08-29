@extends('layouts.dashboard')
@section('content')
<h2>All Notifications</h2>
<table class="table-auto w-full">
<thead><tr><th>Recipient</th><th>Type</th><th>Message</th><th>Actions</th></tr></thead>
<tbody>
@foreach($notifications as $notification)
<tr>
<td>{{ $notification->notifiable->name ?? '-' }}</td>
<td>{{ $notification->type }}</td>
<td>{{ $notification->data['message'] ?? '-' }}</td>
<td><a href="{{ route('admin.notifications.show', $notification->id) }}">View</a></td>
</tr>
@endforeach
</tbody>
</table>
@endsection
