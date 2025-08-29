@extends('layouts.dashboard')
@section('content')
<h2>Edit Opportunity: {{ $opportunity->title }}</h2>
<form method="POST" action="{{ route('admin.opportunities.update', $opportunity->id) }}">
@csrf
<label>Title: <input type="text" name="title" value="{{ $opportunity->title }}"></label><br>
<label>Type: <input type="text" name="type" value="{{ $opportunity->type }}"></label><br>
<label>Description: <textarea name="description">{{ $opportunity->description }}</textarea></label><br>
<button type="submit">Update</button>
</form>
@endsection
