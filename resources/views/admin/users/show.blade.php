@extends('layouts.dashboard')
@section('content')
<div class="max-w-xl mx-auto py-10">
    <h2 class="text-2xl font-bold mb-6 text-indigo-700">User: {{ $user->name }}</h2>

    <!-- Metrics Card -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
            <span class="block text-lg font-bold text-blue-700">{{ $user->email }}</span>
            <span class="text-xs text-gray-500">Email</span>
        </div>
        <div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
            <span class="block text-lg font-bold text-indigo-700">{{ ucfirst($user->role) }}</span>
            <span class="text-xs text-gray-500">Role</span>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-lg p-6 shadow mb-6">
        <ul class="space-y-2">
            <li><span class="font-semibold text-gray-700">Email:</span> {{ $user->email }}</li>
            <li><span class="font-semibold text-gray-700">Role:</span> {{ ucfirst($user->role) }}</li>
            <li><span class="font-semibold text-gray-700">Joined:</span> {{ $user->created_at->diffForHumans() }}</li>
            <li><span class="font-semibold text-gray-700">Last Updated:</span> {{ $user->updated_at->diffForHumans() }}</li>
        </ul>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-indigo-600 text-white px-6 py-2 rounded-md shadow hover:bg-indigo-700">Edit</a>
        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="ml-4">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded-md shadow hover:bg-red-700">Delete</button>
        </form>
    </div>
</div>
@endsection
