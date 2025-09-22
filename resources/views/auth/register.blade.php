<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .toast-container {
        z-index: 2147483647; /* Set to the maximum possible value */
    }
  </style>
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

    <!-- Hidden organization_name field for validation -->
    <input type="hidden" id="organization_name" name="organization_name" value="{{ old('organization_name') }}">

      <!-- Main Fields: Always Visible, but labels/placeholders are dynamic -->
      <div class="space-y-4">
        <label for="name" id="nameLabel" class="block text-sm font-medium text-gray-700">Full Name or Organization Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Your Full Name or Organization Name" />
        @error('name')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror

        <label for="email" id="emailLabel" class="block text-sm font-medium text-gray-700">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
         class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
         placeholder="Your email or organization email" />
        @error('email')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Organization Extra Fields: Only show if role is organization -->
      <div id="organizationFields" class="space-y-4 hidden">
        <h3 class="text-lg font-semibold text-gray-700">Organization Information</h3>
        <label for="organization_profile" class="block text-sm font-medium text-gray-700">Organization Profile</label>
        <textarea id="organization_profile" name="organization_profile" rows="3"
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Brief description of your organization">{{ old('organization_profile') }}</textarea>
        @error('organization_profile')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror

        <label for="industry" class="block text-sm font-medium text-gray-700">Industry</label>
        <input id="industry" type="text" name="industry" value="{{ old('industry') }}"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Industry (e.g. Technology, Education)" />
        @error('industry')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror

        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
        <input id="location" type="text" name="location" value="{{ old('location') }}"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Organization Location (City, Country)" />
        @error('location')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror

        <label for="organization_logo" class="block text-sm font-medium text-gray-700">Organization Logo (optional)</label>
        <input id="organization_logo" type="file" name="organization_logo"
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700"/>
        @error('organization_logo')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror

        <label for="website" class="block text-sm font-medium text-gray-700">Website (optional)</label>
        <input id="website" type="text" name="website" value="{{ old('website') }}"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Organization Website (optional)" />
        @error('website')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror

        <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone (optional)</label>
        <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone') }}"
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Organization Contact Phone (optional)" />
        @error('contact_phone')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Extra fields for Student -->
      <div id="studentFields" class="space-y-4 hidden">
        <h3 class="text-lg font-semibold text-gray-700">Student Information</h3>
        <div>
          <label for="university" class="block text-sm font-medium text-gray-700">University</label>
     <input id="university" type="text" name="university" value="{{ old('university') }}"
       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
       placeholder="Your University" />
          @error('university')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
     <input id="course" type="text" name="course" value="{{ old('course') }}"
       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
       placeholder="Your Course of Study" />
          @error('course')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="year" class="block text-sm font-medium text-gray-700">Year of Study</label>
     <input id="year" type="number" name="year" value="{{ old('year') }}"
       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
       placeholder="Year of Study (e.g. 2)" />
          @error('year')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="academic_field" class="block text-sm font-medium text-gray-700">Academic Field</label>
     <input id="academic_field" type="text" name="academic_field" value="{{ old('academic_field') }}"
       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
       placeholder="Your Academic Field (e.g. Computer Science)" />
          @error('academic_field')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
          <textarea id="bio" name="bio" rows="3"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="A short bio about yourself">{{ old('bio') }}</textarea>
          @error('bio')
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
          <i class="fas fa-user-plus me-2"></i> Register
        </button>
      </div>
  <!-- Toast Notifications: shown if session('status') is set after redirect -->
  @if(session('status'))
    <div class="fixed top-4 right-4 z-50">
      <div class="@if(str_contains(session('status'), 'failed')) bg-red-600 @else bg-green-600 @endif text-white px-6 py-3 rounded-lg shadow flex items-center gap-2">
        <i class="fas @if(str_contains(session('status'), 'failed')) fa-times-circle @else fa-check-circle @endif"></i>
        <span>{{ session('status') }}</span>
        <button onclick="this.parentElement.style.display='none'" class="ml-4 text-white">&times;</button>
      </div>
    </div>
  @endif
  @if(session('resent'))
    <div class="fixed top-20 right-4 z-50">
      <div class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow flex items-center gap-2">
        <i class="fas fa-envelope"></i>
        <span>A verification email has been sent. Please check your inbox.</span>
        <button onclick="this.parentElement.style.display='none'" class="ml-4 text-white">&times;</button>
      </div>
    </div>
  @endif
    </form>
  </div>

  <script>
    const roleSelect = document.getElementById('role');
    const organizationFields = document.getElementById('organizationFields');
    const studentFields = document.getElementById('studentFields');
    const nameLabel = document.getElementById('nameLabel');
    const nameInput = document.getElementById('name');
    const emailLabel = document.getElementById('emailLabel');
    const emailInput = document.getElementById('email');
     const orgNameInput = document.getElementById('organization_name');

    function updateRoleFields() {
      const selectedRole = roleSelect.value;
      organizationFields.classList.add('hidden');
      studentFields.classList.add('hidden');

      // Dynamic labels and placeholders
      if (selectedRole === 'organization') {
        nameLabel.textContent = "Organization Name";
        nameInput.placeholder = "Organization Name";
        emailLabel.textContent = "Organization Email";
        emailInput.placeholder = "Organization email";
        organizationFields.classList.remove('hidden');
         orgNameInput.value = nameInput.value;
         orgNameInput.disabled = false;
      } else if (selectedRole === 'student') {
        nameLabel.textContent = "Full Name";
        nameInput.placeholder = "Your Full Name";
        emailLabel.textContent = "Student Email";
        emailInput.placeholder = "Your student email";
        studentFields.classList.remove('hidden');
         orgNameInput.value = '';
         orgNameInput.disabled = true;
      } else {
        nameLabel.textContent = "Full Name or Organization Name";
        nameInput.placeholder = "Your Full Name or Organization Name";
        emailLabel.textContent = "Email";
        emailInput.placeholder = "Your email or organization email";
         orgNameInput.value = '';
         orgNameInput.disabled = true;
      }
    }
     // Sync organization_name with name field on input
     nameInput.addEventListener('input', function() {
       if (roleSelect.value === 'organization') {
         orgNameInput.value = nameInput.value;
       }
     });
    roleSelect.addEventListener('change', updateRoleFields);
    document.addEventListener('DOMContentLoaded', updateRoleFields);
  </script>
</body>
</html>
