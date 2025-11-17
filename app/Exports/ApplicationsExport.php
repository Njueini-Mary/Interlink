<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicationsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Application::with(['student.user', 'opportunity.skills', 'student.skills'])
            ->get()
            ->map(function ($app) {
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
