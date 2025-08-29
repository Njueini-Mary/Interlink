@extends('layouts.dashboard')
@section('content')
<h2>Edit Application</h2>
<form method="POST" action="{{ route('admin.applications.update', $application->id) }}">
@csrf
<label>Status: <input type="text" name="status" value="{{ $application->status }}"></label><br>
<button type="submit">Update</button>
</form>
@endsection
