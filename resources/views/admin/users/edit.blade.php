@extends('layouts.dashboard')
@section('content')
<h2>Edit User: {{ $user->name }}</h2>
<form method="POST" action="{{ route('admin.users.update', $user->id) }}">
@csrf
<label>Name: <input type="text" name="name" value="{{ $user->name }}"></label><br>
<label>Email: <input type="email" name="email" value="{{ $user->email }}"></label><br>
<label>Role: <input type="text" name="role" value="{{ $user->role }}"></label><br>
<button type="submit">Update</button>
</form>
@endsection
