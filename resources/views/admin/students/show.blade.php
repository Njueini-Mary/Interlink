@extends('layouts.dashboard')
@section('content')
<h2>Student: {{ $student->user->name ?? '-' }}</h2>
<ul>
<li>University: {{ $student->university }}</li>
<li>Course: {{ $student->course }}</li>
<li>Year: {{ $student->year }}</li>
</ul>
<a href="{{ route('admin.students.edit', $student->id) }}">Edit</a>
@endsection
