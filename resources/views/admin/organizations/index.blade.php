@extends('layouts.dashboard')
@section('content')
<h2>All Organizations</h2>
<table class="table-auto w-full">
<thead><tr><th>Name</th><th>Contact Email</th><th>Location</th><th>Actions</th></tr></thead>
<tbody>
@foreach($organizations as $org)
<tr>
<td>{{ $org->name }}</td>
<td>{{ $org->contact_email }}</td>
<td>{{ $org->location }}</td>
<td><a href="{{ route('admin.organizations.show', $org->id) }}">View</a></td>
</tr>
@endforeach
</tbody>
</table>
@endsection
