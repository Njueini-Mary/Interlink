@extends('layouts.dashboard')

@section('content')
<!-- Admin Dashboard - Modernized -->
<div class="max-w-7xl mx-auto py-10">
    <h2 class="font-bold text-3xl mb-8 text-indigo-700">Admin Dashboard</h2>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
        <a href="{{ route('admin.users.index') }}" class="bg-blue-50 rounded-lg p-6 text-center shadow hover:bg-blue-100 transition">
            <div class="text-xs text-gray-500 mb-1">Total Users</div>
            <div class="text-2xl font-bold text-blue-700">{{ $totalUsers }}</div>
        </a>
        <a href="{{ route('admin.students.index') }}" class="bg-green-50 rounded-lg p-6 text-center shadow hover:bg-green-100 transition">
            <div class="text-xs text-gray-500 mb-1">Total Students</div>
            <div class="text-2xl font-bold text-green-700">{{ $totalStudents }}</div>
        </a>
        <a href="{{ route('admin.organizations.index') }}" class="bg-yellow-50 rounded-lg p-6 text-center shadow hover:bg-yellow-100 transition">
            <div class="text-xs text-gray-500 mb-1">Total Organizations</div>
            <div class="text-2xl font-bold text-yellow-700">{{ $totalOrganizations }}</div>
        </a>
        <a href="{{ route('admin.opportunities.index') }}" class="bg-purple-50 rounded-lg p-6 text-center shadow hover:bg-purple-100 transition">
            <div class="text-xs text-gray-500 mb-1">Opportunities</div>
            <div class="text-2xl font-bold text-purple-700">{{ $totalOpportunities }}</div>
        </a>
        <a href="{{ route('admin.applications.index') }}" class="bg-pink-50 rounded-lg p-6 text-center shadow hover:bg-pink-100 transition">
            <div class="text-xs text-gray-500 mb-1">Applications</div>
            <div class="text-2xl font-bold text-pink-700">{{ $totalApplications }}</div>
        </a>
        <a href="#" class="bg-teal-50 rounded-lg p-6 text-center shadow cursor-not-allowed">
            <div class="text-xs text-gray-500 mb-1">Placements</div>
            <div class="text-2xl font-bold text-teal-700">{{ $totalPlacements }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['active' => 1]) }}" class="bg-blue-100 rounded-lg p-6 text-center shadow hover:bg-blue-200 transition">
            <div class="text-xs text-gray-500 mb-1">Active Users</div>
            <div class="text-2xl font-bold text-blue-800">{{ $activeUsers }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['active' => 0]) }}" class="bg-gray-100 rounded-lg p-6 text-center shadow hover:bg-gray-200 transition">
            <div class="text-xs text-gray-500 mb-1">Inactive Users</div>
            <div class="text-2xl font-bold text-gray-700">{{ $inactiveUsers }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['new' => 1]) }}" class="bg-green-100 rounded-lg p-6 text-center shadow hover:bg-green-200 transition">
            <div class="text-xs text-gray-500 mb-1">New Users This Month</div>
            <div class="text-2xl font-bold text-green-800">{{ $newUsersThisMonth }}</div>
        </a>
    </div>

    <!-- Recent Users -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-indigo-700">Recent Users</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($recentUsers as $user)
            <div class="flex items-center bg-white rounded-lg shadow p-4">
                <div class="w-10 h-10 rounded-full bg-indigo-200 flex items-center justify-center text-xl font-bold text-indigo-700 mr-4">{{ strtoupper(substr($user->name,0,1)) }}</div>
                <div>
                    <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500">{{ ucfirst($user->role) }}</div>
                    <div class="text-xs text-gray-400">Joined: {{ $user->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- System Notifications -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-indigo-700">System Notifications</h3>
        <div class="space-y-2">
            @forelse($systemNotifications as $notification)
                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <div class="font-semibold text-gray-700 mb-1"><strong>{{ $notification->title }}</strong> - {{ $notification->message }}</div>
                    <div class="text-xs text-gray-500">
                        @if($notification->read_at) <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded">Read</span> @else <span class="bg-blue-200 text-blue-700 px-2 py-1 rounded">Unread</span> @endif
                        | {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-4">No system notifications.</div>
            @endforelse
        </div>
    </div>

    <!-- System Activity -->
    <div class="mb-10">
        <h3 class="text-lg font-bold mb-4 text-indigo-700">System Activity</h3>
        <div class="space-y-2">
            @forelse($recentActivities as $activity)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                <div class="font-semibold text-gray-700 mb-1">{{ $activity->description }}</div>
                <div class="text-xs text-gray-500">By: {{ $activity->user ? $activity->user->name : 'System' }} | {{ $activity->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow p-4">No recent system activity.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
