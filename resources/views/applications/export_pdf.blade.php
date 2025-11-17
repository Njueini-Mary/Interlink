@extends('layouts.report')

@push('report-styles')
<style>
    @page { size: A4 landscape; margin: 1.2cm; }
    .report-summary { margin-bottom: 18px; font-size: 1.05em; }
    .badge-status-accepted { background: #d1fae5; color: #065f46; border-radius: 6px; padding: 2px 8px; font-weight: 600; }
    .badge-status-pending { background: #fef3c7; color: #92400e; border-radius: 6px; padding: 2px 8px; font-weight: 600; }
    .badge-status-rejected { background: #fee2e2; color: #991b1b; border-radius: 6px; padding: 2px 8px; font-weight: 600; }
    .score-badge { border-radius: 6px; padding: 2px 8px; font-weight: 600; }
    .score-high { background: #d1fae5; color: #065f46; }
    .score-mid { background: #dbeafe; color: #1e40af; }
    .score-low { background: #fee2e2; color: #991b1b; }
    .score-moderate { background: #fef3c7; color: #92400e; }
    .report-table th, .report-table td { font-size: 0.97em; padding: 6px 8px; }
    .report-table th { font-size: 1em; }
</style>
@endpush

@section('report-content')

@php
    $title = isset($orgName) ? ($orgName . ' Applications Export (PDF)') : 'Organization Applications Export (PDF)';
@endphp

<h2 class="text-xl font-bold mb-4">{{ $title }}</h2>

@php
    $total = $applications->count();
    $accepted = $applications->where('status', 'accepted')->count();
    $pending = $applications->where('status', 'pending')->count();
    $rejected = $applications->where('status', 'rejected')->count();
    $avgScore = $applications->avg('match_score');
@endphp
<div class="report-summary">
    <strong>Total Applications:</strong> {{ $total }} &nbsp;|
    <span class="badge-status-accepted">Accepted: {{ $accepted }}</span> &nbsp;
    <span class="badge-status-pending">Pending: {{ $pending }}</span> &nbsp;
    <span class="badge-status-rejected">Rejected: {{ $rejected }}</span> &nbsp;|
    <strong>Average ML Match Score:</strong> {{ number_format($avgScore, 1) }}%
</div>

<table class="report-table" style="table-layout: fixed; width: 100%;">
    <thead>
        <tr>
            <th style="width: 3%">#</th>
            <th style="width: 12%">Student</th>
            <th style="width: 14%">Opportunity</th>
            <th style="width: 8%">Status</th>
            <th style="width: 8%">ML Match</th>
            <th style="width: 15%">Requirements</th>
            <th style="width: 15%">Matched Skills</th>
            <th style="width: 15%">Missing Skills</th>
            <th style="width: 10%">Applied At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($applications as $i => $application)
        @php
            $opportunitySkills = $application->opportunity && $application->opportunity->skills ? $application->opportunity->skills->pluck('name')->toArray() : [];
            $studentSkills = $application->student && $application->student->skills ? $application->student->skills->pluck('name')->toArray() : [];
            $matchedSkills = array_intersect($opportunitySkills, $studentSkills);
            $missingSkills = array_diff($opportunitySkills, $studentSkills);
            $score = $application->match_score;
            $scoreClass = $score >= 80 ? 'score-high' : ($score >= 60 ? 'score-mid' : ($score >= 40 ? 'score-moderate' : 'score-low'));
            $statusClass = 'badge-status-' . strtolower($application->status);
        @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $application->student->user->name ?? '-' }}</td>
            <td>{{ $application->opportunity->title ?? '-' }}</td>
            <td><span class="{{ $statusClass }}">{{ ucfirst($application->status) }}</span></td>
            <td><span class="score-badge {{ $scoreClass }}">{{ $score }}%</span></td>
            <td>{{ implode(', ', $opportunitySkills) }}</td>
            <td>{{ implode(', ', $matchedSkills) }}</td>
            <td>{{ implode(', ', $missingSkills) }}</td>
            <td>{{ $application->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
