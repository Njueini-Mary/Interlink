@extends('layouts.dashboard')
@section('content')
<h2>Edit Notification</h2>
<form method="POST" action="{{ route('admin.notifications.update', $notification->id) }}">
@csrf
<label>Type: <input type="text" name="type" value="{{ $notification->type }}"></label><br>
<label>Message: <input type="text" name="message" value="{{ $notification->data['message'] ?? '' }}"></label><br>
<button type="submit">Update</button>
</form>
@endsection
