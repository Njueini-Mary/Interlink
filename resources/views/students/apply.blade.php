@extends('layouts.dashboard')
@section('title', 'Apply for Opportunity')
@section('content')
<div class="max-w-2xl mx-auto py-10 px-6 bg-gray-50 rounded-lg shadow-lg">
    <h1 class="text-2xl font-extrabold mb-8 text-gray-800 text-center">Apply for Opportunity</h1>
    <form action="{{ route('applications.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="cover_letter" class="block text-gray-700 font-semibold mb-2">Cover Letter</label>
            <textarea name="cover_letter" id="cover_letter" rows="4" class="form-control w-full" required>{{ old('cover_letter') }}</textarea>
        </div>
        <div class="mb-4">
            <label for="skills" class="block text-gray-700 font-semibold mb-2">Select Your Skills</label>
            <select name="skills[]" id="skills" class="form-select w-full" multiple required>
                @foreach($skills as $skill)
                    <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                @endforeach
            </select>
            <small class="text-gray-500">Hold Ctrl (Windows) or Command (Mac) to select multiple skills.</small>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded font-semibold hover:bg-indigo-700">Submit Application</button>
    </form>
</div>
@endsection
