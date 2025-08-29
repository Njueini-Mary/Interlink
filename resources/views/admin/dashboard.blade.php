@extends('layouts.dashboard')

@section('content')
<div class="dashboard-modern">
    <h2 class="font-semibold text-2xl mb-6">Admin Dashboard</h2>
    <div class="dashboard-stats grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="dashboard-card gradient-blue"><div class="dashboard-card-title">Total Users</div><div class="dashboard-card-value">{{ $totalUsers }}</div></div>
        <div class="dashboard-card gradient-green"><div class="dashboard-card-title">Total Students</div><div class="dashboard-card-value">{{ $totalStudents }}</div></div>
        <div class="dashboard-card gradient-yellow"><div class="dashboard-card-title">Total Organizations</div><div class="dashboard-card-value">{{ $totalOrganizations }}</div></div>
        <div class="dashboard-card gradient-purple"><div class="dashboard-card-title">Opportunities</div><div class="dashboard-card-value">{{ $totalOpportunities }}</div></div>
        <div class="dashboard-card gradient-pink"><div class="dashboard-card-title">Applications</div><div class="dashboard-card-value">{{ $totalApplications }}</div></div>
        <div class="dashboard-card gradient-teal"><div class="dashboard-card-title">Placements</div><div class="dashboard-card-value">{{ $totalPlacements }}</div></div>
        <div class="dashboard-card gradient-blue"><div class="dashboard-card-title">Active Users</div><div class="dashboard-card-value">{{ $activeUsers }}</div></div>
        <div class="dashboard-card gradient-gray"><div class="dashboard-card-title">Inactive Users</div><div class="dashboard-card-value">{{ $inactiveUsers }}</div></div>
        <div class="dashboard-card gradient-green"><div class="dashboard-card-title">New Users This Month</div><div class="dashboard-card-value">{{ $newUsersThisMonth }}</div></div>
    </div>
    <div class="dashboard-section">
        <h3>Recent Users</h3>
        <div class="dashboard-list">
            @foreach($recentUsers as $user)
            <div class="dashboard-list-item">
                <div class="user-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
                <div>
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-meta">{{ ucfirst($user->role) }}</div>
                    <div class="user-meta">Joined: {{ $user->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="dashboard-section">
        <h3>System Notifications</h3>
        <div class="dashboard-list">
            @forelse($systemNotifications as $notification)
                <div class="dashboard-list-item notification">
                    <div class="user-meta"><strong>{{ $notification->title }}</strong> - {{ $notification->message }}</div>
                    <div class="user-meta">@if($notification->read_at) <span class="badge badge-gray">Read</span> @else <span class="badge badge-blue">Unread</span> @endif | {{ $notification->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="dashboard-list-item">No system notifications.</div>
            @endforelse
        </div>
    </div>
    <div class="dashboard-section">
        <h3>System Activity</h3>
        <div class="dashboard-list">
            @forelse($recentActivities as $activity)
            <div class="dashboard-list-item activity">
                <div>
                    <div class="activity-desc">{{ $activity->description }}</div>
                    <div class="user-meta">By: {{ $activity->user ? $activity->user->name : 'System' }} | {{ $activity->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div class="dashboard-list-item">No recent system activity.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
