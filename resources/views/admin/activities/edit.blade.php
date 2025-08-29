@extends('layouts.dashboard')
@section('content')
<h2>Edit Activity</h2>
<form method="POST" action="{{ route('admin.activities.update', $activity->id) }}">
@csrf
<label>Action: <input type="text" name="action" value="{{ $activity->action }}"></label><br>
<label>Target Type: <input type="text" name="target_type" value="{{ $activity->target_type }}"></label><br>
<label>Target ID: <input type="text" name="target_id" value="{{ $activity->target_id }}"></label><br>
<button type="submit">Update</button>
</form>
@endsection
