@extends('layouts.dashboard')
@section('content')
<h2>All Opportunities</h2>
<table class="table-auto w-full">
<thead><tr><th>Title</th><th>Organization</th><th>Type</th><th>Actions</th></tr></thead>
<tbody>
@foreach($opportunities as $opportunity)
<tr>
<td>{{ $opportunity->title }}</td>
<td>{{ $opportunity->organization->name ?? '-' }}</td>
<td>{{ $opportunity->type }}</td>
<td><a href="{{ route('admin.opportunities.show', $opportunity->id) }}">View</a></td>
</tr>
@endforeach
</tbody>
</table>
@endsection
