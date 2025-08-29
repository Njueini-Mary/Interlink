@extends('layouts.dashboard')
@section('content')
<h2>Opportunity: {{ $opportunity->title }}</h2>
<ul>
<li>Organization: {{ $opportunity->organization->name ?? '-' }}</li>
<li>Type: {{ $opportunity->type }}</li>
<li>Description: {{ $opportunity->description }}</li>
</ul>
<a href="{{ route('admin.opportunities.edit', $opportunity->id) }}">Edit</a>
@endsection
