@extends('layouts.dashboard')
@section('content')
<h2>Organization: {{ $organization->name }}</h2>
<ul>
<li>Email: {{ $organization->contact_email }}</li>
<li>Location: {{ $organization->location }}</li>
<li>Industry: {{ $organization->industry }}</li>
</ul>
<a href="{{ route('admin.organizations.edit', $organization->id) }}">Edit</a>
@endsection
