<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Student;
use App\Models\Opportunity;
use Illuminate\Support\Str;

class ApplicationSeeder extends Seeder
{
    public function run()
    {
        $students = Student::with('skills')->take(5)->get();
        $opportunities = Opportunity::with('skills')->take(5)->get();
        $statuses = ['submitted', 'accepted', 'rejected'];
        $now = now();
        $matcher = new \App\Services\SkillMatcher();
        $pairCount = 0;
        $maxPairs = 10;
        foreach ($students as $student) {
            foreach ($opportunities as $opportunity) {
                if ($pairCount >= $maxPairs) break 2;
                $studentSkills = $student->skills->pluck('name')->toArray();
                $opportunitySkills = $opportunity->skills->pluck('name')->toArray();
                $studentProfile = implode(' ', [
                    $student->bio ?? '',
                    $student->interests ?? '',
                    $student->activities ?? '',
                    $student->course ?? '',
                    $student->year ?? '',
                    $student->university ?? '',
                    $student->academic_field ?? '',
                    $student->gpa ?? '',
                    $student->is_available ? 'available' : 'not available',
                    $student->rating ?? '',
                    $student->rating_count ?? '',
                    $student->resume ?? '',
                    $student->organizations ?? ''
                ]);
                $opportunityProfile = implode(' ', [
                    $opportunity->description ?? '',
                    $opportunity->title ?? '',
                    $opportunity->industry ?? '',
                    $opportunity->min_gpa ?? '',
                    $opportunity->interests ?? '',
                    $opportunity->activities ?? '',
                    $opportunity->organizations ?? '',
                    $opportunity->course ?? '',
                    $opportunity->academic_field ?? ''
                ]);
                $mlScores = $matcher->getMLScores(
                    implode(' ', $studentSkills) . ' ' . $studentProfile,
                    implode(' ', $opportunitySkills) . ' ' . $opportunityProfile
                );
                Application::create([
                    'id' => (string) Str::uuid(),
                    'student_id' => $student->id,
                    'opportunity_id' => $opportunity->id,
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $now->subDays(rand(0, 30)),
                    'updated_at' => $now->subDays(rand(0, 30)),
                    'match_score' => $mlScores['tfidf'],
                    'tfidf_score' => $mlScores['tfidf'],
                    'logreg_score' => $mlScores['logreg'],
                    'rf_score' => $mlScores['rf'],
                ]);
                $pairCount++;
            }
        }
    }
}
