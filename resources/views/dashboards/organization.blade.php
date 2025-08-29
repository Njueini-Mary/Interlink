
@extends('layouts.dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Organization Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="dashboard-modern">
    <div class="dashboard-stats">
        <div class="dashboard-card gradient-blue">
            <div class="dashboard-card-title">Open Opportunities</div>
            <div class="dashboard-card-value">{{ $openOpportunities ?? 0 }}</div>
            <div class="dashboard-card-sub">{{ $opportunitiesChange ?? '+0' }} from last month</div>
        </div>
        <div class="dashboard-card gradient-purple">
            <div class="dashboard-card-title">Placements Secured</div>
            <div class="dashboard-card-value">{{ $placementsSecured ?? 0 }}</div>
            <div class="dashboard-card-sub">{{ $placementsChange ?? '+0' }} from last month</div>
        </div>
        <div class="dashboard-card gradient-yellow">
            <div class="dashboard-card-title">Organization Rating</div>
            <div class="dashboard-card-value">{{ $rating ?? 'N/A' }}/5</div>
        </div>
    </div>
    <div class="dashboard-section">
        <h3>Recent Opportunities</h3>
        <div class="dashboard-list">
            @foreach($opportunities as $opportunity)
            <div class="dashboard-list-item">
                <div>
                    <div class="user-name">{{ $opportunity->title }}</div>
                    <div class="user-meta">Applicants: {{ $opportunity->applications_count ?? 0 }}</div>
                    <div class="user-meta">Status: {{ ucfirst($opportunity->status) }}</div>
                </div>
            </div>
            @endforeach
            @if(empty($opportunities) || count($opportunities) == 0)
                <div class="dashboard-list-item">No recent opportunities found.</div>
            @endif
        </div>
    </div>
    <div class="dashboard-section">
        <h3>Recent Student Applications</h3>
        <div class="dashboard-list">
            @foreach($applications ?? [] as $application)
            <div class="dashboard-list-item">
                <div>
                    <div class="user-name">{{ $application->student->name }}</div>
                    <div class="user-meta">Applied for: {{ $application->opportunity->title }}</div>
                    <div class="user-meta">{{ $application->created_at->diffForHumans() }}</div>
                    <div class="user-meta">Status: {{ ucfirst($application->status) }}</div>
                </div>
            </div>
            @endforeach
            @if(empty($applications) || count($applications) == 0)
                <div class="dashboard-list-item">No recent student applications.</div>
            @endif
        </div>
    </div>
</div>
@endsection
