@extends('layouts.dashboard')
@section('content')
<h2>Notification Details</h2>
<ul>
<li>Recipient: {{ $notification->notifiable->name ?? '-' }}</li>
<li>Type: {{ $notification->type }}</li>
<li>Message: {{ $notification->data['message'] ?? '-' }}</li>
<li>Created: {{ $notification->created_at }}</li>
</ul>
<a href="{{ route('admin.notifications.edit', $notification->id) }}">Edit</a>
@endsection
