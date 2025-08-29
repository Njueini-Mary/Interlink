@extends('layouts.dashboard')
@section('content')
<h2>Edit Organization: {{ $organization->name }}</h2>
<form method="POST" action="{{ route('admin.organizations.update', $organization->id) }}">
@csrf
<label>Name: <input type="text" name="name" value="{{ $organization->name }}"></label><br>
<label>Email: <input type="email" name="contact_email" value="{{ $organization->contact_email }}"></label><br>
<label>Location: <input type="text" name="location" value="{{ $organization->location }}"></label><br>
<button type="submit">Update</button>
</form>
@endsection
