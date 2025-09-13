@extends('layouts.dashboard')
@section('content')

<!-- Organization Create View - Modernized Card Layout -->
<div class="max-w-4xl mx-auto py-10">
  <h2 class="text-3xl font-bold mb-8 text-indigo-700">Add Organization</h2>

  {{-- Error Display --}}
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

  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
    <!-- Organization Details Form Card -->
    <div class="bg-gray-50 rounded-lg p-6 shadow">
      <h3 class="text-xl font-semibold mb-4 text-gray-800">Basic Info</h3>
      <form method="POST" action="{{ route('admin.organizations.store') }}" class="space-y-6">
        @csrf
        <!-- Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
          <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <!-- Industry -->
        <div>
          <label for="industry" class="block text-sm font-medium text-gray-700">Industry</label>
          <input type="text" name="industry" id="industry" value="{{ old('industry') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <!-- Location -->
        <div>
          <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
          <input type="text" name="location" id="location" value="{{ old('location') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <!-- Contact Email -->
        <div>
          <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
          <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <!-- Contact Phone -->
        <div>
          <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
          <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <!-- Website -->
        <div>
          <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
          <input type="url" name="website" id="website" value="{{ old('website') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <!-- Profile -->
        <div>
          <label for="profile" class="block text-sm font-medium text-gray-700">Profile</label>
          <textarea name="profile" id="profile" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('profile') }}</textarea>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Add Organization</button>
        </div>
      </form>
    </div>

    <!-- Future Metrics & Related Data Card (placeholder for consistency) -->
    <div class="bg-white rounded-lg p-6 shadow flex flex-col justify-center items-center">
      {{-- This card is reserved for future metrics, analytics, or quick tips for admins --}}
      <span class="text-gray-400 text-center">Metrics and analytics will appear here after organization creation.</span>
    </div>
  </div>
</div>
@endsection
