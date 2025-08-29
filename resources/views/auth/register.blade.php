<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
  <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-lg">
  <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Register for Interlink</h2>
  <p class="mb-4 text-center text-gray-500">Create your account to access internships, placements, and organization features.</p>
    <form method="POST" action="{{ route('register') }}" id="registrationForm" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <!-- Role Selection (Student, Organization, Admin) -->
      <div>
        <label for="role" class="block text-sm font-medium text-gray-700">Registering as</label>
        <select id="role" name="role" required
                class="mt-1 block w-full px-4 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">-- Select Role --</option>
          <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
          <option value="organization" {{ old('role') == 'organization' ? 'selected' : '' }}>Organization</option>
        </select>
        @error('role')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- General Fields -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        @error('name')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        @error('email')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Extra fields for Organization -->
      <div id="organizationFields" class="space-y-4 hidden">
        <h3 class="text-lg font-semibold text-gray-700">Organization Information</h3>
        <div>
          <label for="organization_name" class="block text-sm font-medium text-gray-700">Organization Name</label>
          <input id="organization_name" type="text" name="organization_name" value="{{ old('organization_name') }}"
                 class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          @error('organization_name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="organization_profile" class="block text-sm font-medium text-gray-700">Organization Profile</label>
          <textarea id="organization_profile" name="organization_profile" rows="3"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('organization_profile') }}</textarea>
          @error('organization_profile')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <!-- Organization Logo file upload -->
        <div>
          <label for="organization_logo" class="block text-sm font-medium text-gray-700">Organization Logo</label>
          <input id="organization_logo" type="file" name="organization_logo"
                 class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700"/>
          @error('organization_logo')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
          <input id="location" type="text" name="location" value="{{ old('location') }}"
                 class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          @error('location')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <!-- Extra fields for Student -->
      <div id="studentFields" class="space-y-4 hidden">
        <h3 class="text-lg font-semibold text-gray-700">Student Information</h3>
        <div>
          <label for="university" class="block text-sm font-medium text-gray-700">University</label>
          <input id="university" type="text" name="university" value="{{ old('university') }}"
                 class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          @error('university')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
          <input id="course" type="text" name="course" value="{{ old('course') }}"
                 class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          @error('course')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="year" class="block text-sm font-medium text-gray-700">Year of Study</label>
          <input id="year" type="number" name="year" value="{{ old('year') }}"
                 class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          @error('year')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="resume" class="block text-sm font-medium text-gray-700">Resume/CV</label>
          <input id="resume" type="file" name="resume"
                 class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700"/>
          @error('resume')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        @error('password')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        @error('password_confirmation')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Register Button -->
      <div class="flex items-center justify-between pt-4">
        <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline">
          Already registered?
        </a>
        <button type="submit"
                class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          Register
        </button>
      </div>
    </form>
  </div>

  <script>
    const roleSelect = document.getElementById('role');
    const organizationFields = document.getElementById('organizationFields');
    const studentFields = document.getElementById('studentFields');

    function updateRoleFields() {
      const selectedRole = roleSelect.value;
      organizationFields.classList.add('hidden');
      studentFields.classList.add('hidden');
      // Remove required attributes
      ['organization_name','organization_profile','location'].forEach(id => { const el = document.getElementById(id); if(el) el.required = false; });
      ['university','course','year'].forEach(id => { const el = document.getElementById(id); if(el) el.required = false; });
      if (selectedRole === 'organization') {
        organizationFields.classList.remove('hidden');
        ['organization_name','organization_profile','location'].forEach(id => { const el = document.getElementById(id); if(el) el.required = true; });
      } else if (selectedRole === 'student') {
        studentFields.classList.remove('hidden');
        ['university','course','year'].forEach(id => { const el = document.getElementById(id); if(el) el.required = true; });
      }
    }
    roleSelect.addEventListener('change', updateRoleFields);
    document.addEventListener('DOMContentLoaded', updateRoleFields);
  </script>
</body>
</html>
