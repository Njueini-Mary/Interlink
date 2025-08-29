@extends('layouts.dashboard')
@section('content')
<h2>User: {{ $user->name }}</h2>
<ul>
<li>Email: {{ $user->email }}</li>
<li>Role: {{ ucfirst($user->role) }}</li>
<li>Joined: {{ $user->created_at->diffForHumans() }}</li>
</ul>
<a href="{{ route('admin.users.edit', $user->id) }}">Edit</a>
@endsection
