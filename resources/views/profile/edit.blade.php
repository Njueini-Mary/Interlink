<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Update Profile Information -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

         

            <!-- Student Specific Fields -->
            @if(Auth::user()->hasRole('student'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Student Profile</h3>

                        <form action="" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="skills" class="block text-sm font-medium text-gray-700">Skills</label>
                                <input type="text" name="skills" id="skills" class="mt-1 block w-full rounded-md shadow-sm border-gray-300" placeholder="e.g. Programming, Data Analysis" value="{{ old('skills', $student->skills ?? '') }}">
                            </div>

                            <div class="mb-4">
                                <label for="academic_field" class="block text-sm font-medium text-gray-700">Academic Field</label>
                                <input type="text" name="academic_field" id="academic_field" class="mt-1 block w-full rounded-md shadow-sm border-gray-300" placeholder="e.g. Computer Science, Business" value="{{ old('academic_field', $student->academic_field ?? '') }}">
                            </div>

                            <div class="mb-4">
                                <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                                <textarea name="bio" id="bio" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300" placeholder="Describe yourself">{{ old('bio', $student->bio ?? '') }}</textarea>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">
                                    Save Student Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
