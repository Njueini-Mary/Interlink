@extends('layouts.dashboard')
@section('content')

<!-- Student Edit View - Modern Card Layout -->
<div class="max-w-2xl mx-auto py-10">
    <h2 class="text-2xl font-bold mb-6 text-indigo-700">Edit {{ $student->user->name ?? 'Student' }}</h2>

    @if ($errors->any())
        <div class="mb-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="list-disc list-inside text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-gray-50 rounded-lg p-6 shadow">
        <form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="space-y-6">
            @csrf
            <div>
                <label for="university" class="block text-sm font-medium text-gray-700">University</label>
                <input type="text" name="university" id="university" value="{{ old('university', $student->university) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                <input type="text" name="course" id="course" value="{{ old('course', $student->course) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                <input type="text" name="year" id="year" value="{{ old('year', $student->year) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="academic_field" class="block text-sm font-medium text-gray-700">Academic Field</label>
                <input type="text" name="academic_field" id="academic_field" value="{{ old('academic_field', $student->academic_field) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="is_available" class="block text-sm font-medium text-gray-700">Available</label>
                <select name="is_available" id="is_available" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="1" @if(old('is_available', $student->is_available)) selected @endif>Yes</option>
                    <option value="0" @if(!old('is_available', $student->is_available)) selected @endif>No</option>
                </select>
            </div>
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                <textarea name="bio" id="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $student->bio) }}</textarea>
            </div>
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.students.show', $student->id) }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md shadow hover:bg-gray-300">Back</a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Update Student</button>
            </div>
        </form>
    </div>
</div>

{{-- End of Student Edit View --}}
@endsection
