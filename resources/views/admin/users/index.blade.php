@extends('layouts.dashboard')
@section('content')
<h2>All Users</h2>
<table class="table-auto w-full">
<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead>
<tbody>
@foreach($users as $user)
<tr>
<td>{{ $user->name }}</td>
<td>{{ $user->email }}</td>
<td>{{ ucfirst($user->role) }}</td>
<td>{{ $user->created_at->diffForHumans() }}</td>
<td><a href="{{ route('admin.users.show', $user->id) }}">View</a></td>
</tr>
@endforeach
</tbody>
</table>
@endsection
