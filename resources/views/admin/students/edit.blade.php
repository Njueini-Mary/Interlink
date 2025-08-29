@extends('layouts.dashboard')
@section('content')
<h2>Edit Student: {{ $student->user->name ?? '-' }}</h2>
<form method="POST" action="{{ route('admin.students.update', $student->id) }}">
@csrf
<label>University: <input type="text" name="university" value="{{ $student->university }}"></label><br>
<label>Course: <input type="text" name="course" value="{{ $student->course }}"></label><br>
<label>Year: <input type="text" name="year" value="{{ $student->year }}"></label><br>
<button type="submit">Update</button>
</form>
@endsection
