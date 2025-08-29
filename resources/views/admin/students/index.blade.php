@extends('layouts.dashboard')
@section('content')
<h2>All Students</h2>
<table class="table-auto w-full">
<thead><tr><th>Name</th><th>University</th><th>Course</th><th>Actions</th></tr></thead>
<tbody>
@foreach($students as $student)
<tr>
<td>{{ $student->user->name ?? '-' }}</td>
<td>{{ $student->university }}</td>
<td>{{ $student->course }}</td>
<td><a href="{{ route('admin.students.show', $student->id) }}">View</a></td>
</tr>
@endforeach
</tbody>
</table>
@endsection
