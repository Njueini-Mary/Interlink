
@extends('layouts.dashboard')

@section('content')
<div class="dashboard-modern">
    <div class="dashboard-stats grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="dashboard-card gradient-blue">
            <div class="dashboard-card-title">Total Users</div>
            <div class="dashboard-card-value">{{ $totalUsers }}</div>
        </div>
        <div class="dashboard-card gradient-green">
            <div class="dashboard-card-title">New Users This Month</div>
            <div class="dashboard-card-value">{{ $newUsersThisMonth }}</div>
        </div>
        <div class="dashboard-card gradient-orange">
            <div class="dashboard-card-title">Active Users</div>
            <div class="dashboard-card-value">{{ $activeUsers }}</div>
        </div>
        <div class="dashboard-card gradient-gray">
            <div class="dashboard-card-title">Inactive Users</div>
            <div class="dashboard-card-value">{{ $inactiveUsers }}</div>
        </div>
        <div class="dashboard-card gradient-yellow">
            <div class="dashboard-card-title">Organizations</div>
            <div class="dashboard-card-value">{{ $totalOrganizations }}</div>
        </div>
        <div class="dashboard-card gradient-orange">
            <div class="dashboard-card-title">Students</div>
            <div class="dashboard-card-value">{{ $totalStudents }}</div>
        </div>
        <div class="dashboard-card gradient-pink">
            <div class="dashboard-card-title">Opportunities</div>
            <div class="dashboard-card-value">{{ $totalOpportunities }}</div>
        </div>
        <div class="dashboard-card gradient-purple">
            <div class="dashboard-card-title">Applications</div>
            <div class="dashboard-card-value">{{ $totalApplications }}</div>
        </div>
        <div class="dashboard-card gradient-teal">
            <div class="dashboard-card-title">Placements Secured</div>
            <div class="dashboard-card-value">{{ $totalPlacements }}</div>
        </div>
    </div>
    <div class="dashboard-quicklinks mb-8">
        <h3 class="mb-2">Quick Admin Links</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-blue">Users</a>
            <a href="{{ route('admin.organizations.index') }}" class="btn btn-sm btn-yellow">Organizations</a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-orange">Students</a>
            <a href="{{ route('admin.opportunities.index') }}" class="btn btn-sm btn-pink">Opportunities</a>
            <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-purple">Applications</a>
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-teal">Notifications</a>
            <a href="{{ route('admin.activities.index') }}" class="btn btn-sm btn-gray">Activities</a>
        </div>
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
        <h3>Analytics Overview</h3>
        <div class="dashboard-list">
            <div class="dashboard-list-item">
                <canvas id="adminAnalyticsChart" height="80"></canvas>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                @php
                    $adminChartData = [
                        $totalUsers,
                        $totalOrganizations,
                        $totalStudents,
                        $totalOpportunities,
                        $totalApplications,
                        $totalPlacements
                    ];
                @endphp
                <script>
                let adminChartData = @json($adminChartData);
                </script>
                <script>
                const ctx = document.getElementById('adminAnalyticsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Users', 'Organizations', 'Students', 'Opportunities', 'Applications', 'Placements'],
                        datasets: [{
                            label: 'Totals',
                            data: adminChartData,
                            backgroundColor: [
                                'rgba(59,130,246,0.7)',
                                'rgba(251,191,36,0.7)',
                                'rgba(251,146,60,0.7)',
                                'rgba(236,72,153,0.7)',
                                'rgba(139,92,246,0.7)',
                                'rgba(16,185,129,0.7)'
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } }
                    }
                });
                </script>
            </div>
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
    <!-- Admin CRUD Panels -->
    <div class="dashboard-section">
        <h3>Categories Management</h3>
        <div class="dashboard-list">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-blue mb-2">Create New Category</a>
            <table class="table-auto w-full text-left">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentCategories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>
                            <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-xs btn-gray">View</a>
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-xs btn-yellow">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-red" onclick="return confirm('Delete this category?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="dashboard-section">
        <h3>Reviews Management</h3>
        <div class="dashboard-list">
            <a href="{{ route('admin.reviews.create') }}" class="btn btn-sm btn-blue mb-2">Create New Review</a>
            <table class="table-auto w-full text-left">
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Reviewee</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentReviews as $review)
                    <tr>
                        <td>{{ $review->reviewer->name ?? '-' }}</td>
                        <td>{{ $review->reviewee->name ?? '-' }}</td>
                        <td>{{ $review->rating }}</td>
                        <td>
                            <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-xs btn-gray">View</a>
                            <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-xs btn-yellow">Edit</a>
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-red" onclick="return confirm('Delete this review?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="dashboard-section">
        <h3>Skills Management</h3>
        <div class="dashboard-list">
            <a href="{{ route('admin.skills.create') }}" class="btn btn-sm btn-blue mb-2">Create New Skill</a>
            <table class="table-auto w-full text-left">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSkills as $skill)
                    <tr>
                        <td>{{ $skill->name }}</td>
                        <td>
                            <a href="{{ route('admin.skills.show', $skill->id) }}" class="btn btn-xs btn-gray">View</a>
                            <a href="{{ route('admin.skills.edit', $skill->id) }}" class="btn btn-xs btn-yellow">Edit</a>
                            <form action="{{ route('admin.skills.destroy', $skill->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-red" onclick="return confirm('Delete this skill?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="dashboard-section">
        <h3>Tasks Management</h3>
        <div class="dashboard-list">
            <a href="{{ route('admin.tasks.create') }}" class="btn btn-sm btn-blue mb-2">Create New Task</a>
            <table class="table-auto w-full text-left">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td>{{ ucfirst($task->status) }}</td>
                        <td>
                            <a href="{{ route('admin.tasks.show', $task->id) }}" class="btn btn-xs btn-gray">View</a>
                            <a href="{{ route('admin.tasks.edit', $task->id) }}" class="btn btn-xs btn-yellow">Edit</a>
                            <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-red" onclick="return confirm('Delete this task?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="dashboard-section">
        <h3>TimeLogs Management</h3>
        <div class="dashboard-list">
            <a href="{{ route('admin.timelogs.create') }}" class="btn btn-sm btn-blue mb-2">Create New TimeLog</a>
            <table class="table-auto w-full text-left">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Task</th>
                        <th>Hours</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTimeLogs as $timelog)
                    <tr>
                        <td>{{ $timelog->user->name ?? '-' }}</td>
                        <td>{{ $timelog->task->title ?? '-' }}</td>
                        <td>{{ $timelog->hours }}</td>
                        <td>
                            <a href="{{ route('admin.timelogs.show', $timelog->id) }}" class="btn btn-xs btn-gray">View</a>
                            <a href="{{ route('admin.timelogs.edit', $timelog->id) }}" class="btn btn-xs btn-yellow">Edit</a>
                            <form action="{{ route('admin.timelogs.destroy', $timelog->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-red" onclick="return confirm('Delete this timelog?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection