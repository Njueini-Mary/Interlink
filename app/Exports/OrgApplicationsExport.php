<?php

namespace App\Exports;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrgApplicationsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $user = Auth::user();
        $orgId = $user->organizationProfile->id ?? null;
        $apps = Application::whereHas('opportunity', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })
        ->with(['student.user', 'opportunity.skills', 'student.skills'])
        ->get();
        return $apps->map(function ($app) {
            $opportunitySkills = $app->opportunity && $app->opportunity->skills ? $app->opportunity->skills->pluck('name')->toArray() : [];
            $studentSkills = $app->student && $app->student->skills ? $app->student->skills->pluck('name')->toArray() : [];
            $matchedSkills = array_intersect($opportunitySkills, $studentSkills);
            $missingSkills = array_diff($opportunitySkills, $studentSkills);
            return [
                $app->student->user->name ?? '-',
                $app->opportunity->title ?? '-',
                ucfirst($app->status),
                $app->match_score,
                implode(', ', $opportunitySkills),
                implode(', ', $matchedSkills),
                implode(', ', $missingSkills),
                $app->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student',
            'Opportunity',
            'Status',
            'ML Match Score',
            'Opportunity Requirements',
            'Matched Skills',
            'Missing Skills',
            'Applied At',
        ];
    }
}
