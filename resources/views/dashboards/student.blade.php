
@extends('layouts.dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Student Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="dashboard-modern">
    <div class="dashboard-stats">
        <div class="dashboard-card gradient-blue">
            <div class="dashboard-card-title">Active Opportunities</div>
            <div class="dashboard-card-value">{{ $activeOpportunities ?? 0 }}</div>
            <div class="dashboard-card-sub">{{ $opportunitiesChange ?? '+0' }} from last month</div>
        </div>
        <div class="dashboard-card gradient-green">
            <div class="dashboard-card-title">Applications Submitted</div>
            <div class="dashboard-card-value">{{ $applicationsSubmitted ?? 0 }}</div>
            <div class="dashboard-card-sub">{{ $applicationsChange ?? '+0' }} from last month</div>
        </div>
        <div class="dashboard-card gradient-purple">
            <div class="dashboard-card-title">Interviews Scheduled</div>
            <div class="dashboard-card-value">{{ $interviewsScheduled ?? 0 }}</div>
            <div class="dashboard-card-sub">{{ $interviewsChange ?? '+0' }} from last month</div>
        </div>
        <div class="dashboard-card gradient-orange">
            <div class="dashboard-card-title">Placements Secured</div>
            <div class="dashboard-card-value">{{ $placementsSecured ?? 0 }}</div>
            <div class="dashboard-card-sub">{{ $placementsChange ?? '+0' }} from last month</div>
        </div>
    </div>
    <div class="dashboard-section">
        <h3>My Applications</h3>
        <div class="dashboard-list">
            @foreach ($applications ?? [] as $application)
            <div class="dashboard-list-item">
                <div>
                    <div class="user-name">{{ $application->opportunity->title }}</div>
                    <div class="user-meta">Organization: {{ $application->opportunity->organization->name }}</div>
                    <div class="user-meta">Status: {{ ucfirst($application->status) }}</div>
                    <div class="user-meta">Applied: {{ $application->created_at->format('M d, Y') }}</div>
                </div>
            </div>
            @endforeach
            @if(empty($applications) || count($applications) == 0)
                <div class="dashboard-list-item">No applications found.</div>
            @endif
        </div>
    </div>
    <div class="dashboard-section">
        <h3>Upcoming Interviews</h3>
        <div class="dashboard-list">
            @foreach ($interviews ?? [] as $interview)
            <div class="dashboard-list-item">
                <div>
                    <div class="user-name">{{ $interview->opportunity->title }}</div>
                    <div class="user-meta">Date: {{ $interview->scheduled_at->format('M d, Y H:i') }}</div>
                    <div class="user-meta">Status: {{ ucfirst($interview->status) }}</div>
                </div>
            </div>
            @endforeach
            @if(empty($interviews) || count($interviews) == 0)
                <div class="dashboard-list-item">No upcoming interviews scheduled.</div>
            @endif
        </div>
    </div>
    <div class="dashboard-section">
        <h3>Placement Status</h3>
        <div class="dashboard-list">
            <div class="dashboard-list-item">
                @if($placementsSecured ?? 0 > 0)
                    <span class="badge bg-success">Placed</span>
                    <div class="user-meta">You have secured {{ $placementsSecured }} placement(s).</div>
                @else
                    <span class="badge bg-warning">Seeking Placement</span>
                    <div class="user-meta">No placements secured yet.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="dashboard-section">
        <h3>Recent Messages from Organizations</h3>
        <div class="dashboard-list">
            @foreach($messages ?? [] as $message)
            <div class="dashboard-list-item">
                <div>
                    <div class="user-name">{{ $message->organization->name }}</div>
                    <div class="user-meta">{{ $message->created_at->diffForHumans() }}</div>
                    <div class="user-meta">{{ $message->content }}</div>
                </div>
            </div>
            @endforeach
            @if(empty($messages) || count($messages) == 0)
                <div class="dashboard-list-item">No recent messages from organizations.</div>
            @endif
        </div>
    </div>
</div>
@endsection