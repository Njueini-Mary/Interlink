<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Student;
use App\Models\Opportunity;
use App\Services\SkillMatcher;
use Barryvdh\DomPDF\Facade\Pdf;

class MLMatchingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function ml_match_scores_are_reasonable_and_xai_fields_align()
    {
        $students = Student::with('skills')->take(5)->get();
        $opportunities = Opportunity::with('skills')->take(5)->get();
        $this->assertNotEmpty($students, 'No students found');
        $this->assertNotEmpty($opportunities, 'No opportunities found');

        $matcher = new SkillMatcher();
        $reportSummaries = [];
        $allResults = [];
        $studentIdx = 1;
        $terminalHeader = "\n==============================\n  ML MATCHING TEST REPORT\n==============================\n";
        fwrite(STDOUT, $terminalHeader);
        $pairCount = 0;
        $maxPairs = 10;
        $highScoreFound = false;
        foreach ($students as $student) {
            foreach ($opportunities as $opportunity) {
                if ($pairCount >= $maxPairs) break 2;
                $studentSkills = $student->skills->pluck('name')->toArray();
                $opportunitySkills = $opportunity->skills->pluck('name')->toArray();
                $studentArr = [
                    'skills' => $studentSkills,
                    'course' => $student->course ?? '',
                    'year' => $student->year ?? '',
                    'university' => $student->university ?? '',
                    'academic_field' => $student->academic_field ?? '',
                    'gpa' => $student->gpa ?? '',
                    'interests' => is_array($student->interests) ? $student->interests : (is_string($student->interests) ? array_map('trim', explode(',', $student->interests)) : []),
                ];
                $opportunityArr = [
                    'skills' => $opportunitySkills,
                    'course' => $opportunity->course ?? '',
                    'year' => $opportunity->year ?? '',
                    'university' => $opportunity->university ?? '',
                    'academic_field' => $opportunity->academic_field ?? '',
                    'gpa' => $opportunity->min_gpa ?? '',
                    'interests' => is_array($opportunity->interests) ? $opportunity->interests : (is_string($opportunity->interests) ? array_map('trim', explode(',', $opportunity->interests)) : []),
                ];
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
                $studentText = implode(' ', $studentSkills) . ' ' . $studentProfile;
                $opportunityText = implode(' ', $opportunitySkills) . ' ' . $opportunityProfile;
                $mlScores = $matcher->getMLScores($studentText, $opportunityText);
                if ($mlScores['rf'] >= 70 || $mlScores['tfidf'] >= 70 || $mlScores['logreg'] >= 70) {
                    $highScoreFound = true;
                }

                // Feature-level matching
                $featureMatches = $matcher->getFeatureMatches($studentArr, $opportunityArr);

                // Modern terminal output
                fwrite(STDOUT, "\n------------------------------\n");
                fwrite(STDOUT, "Student: \033[1;36m{$student->name}\033[0m | Opportunity: \033[1;33m{$opportunity->title}\033[0m\n");
                fwrite(STDOUT, "Scores: RF=\033[1;34m{$mlScores['rf']}\033[0m, TFIDF=\033[1;36m{$mlScores['tfidf']}\033[0m, LOGREG=\033[1;35m{$mlScores['logreg']}\033[0m\n");
                foreach ($featureMatches as $feature => $vals) {
                    fwrite(STDOUT, ucfirst($feature) . " Matched: \033[1;32m[" . implode(', ', $vals['matched']) . "]\033[0m | Missing: \033[1;31m[" . implode(', ', $vals['missing']) . "]\033[0m\n");
                }

                $this->assertIsArray($mlScores, 'ML scores should be an array');
                $this->assertArrayHasKey('rf', $mlScores, 'ML scores should have RF key');
                $this->assertArrayHasKey('tfidf', $mlScores, 'ML scores should have TFIDF key');
                $this->assertArrayHasKey('logreg', $mlScores, 'ML scores should have LOGREG key');
                $this->assertGreaterThanOrEqual(0, $mlScores['rf'], 'RF score should be >= 0');
                $this->assertLessThanOrEqual(100, $mlScores['rf'], 'RF score should be <= 100');
                $this->assertGreaterThanOrEqual(0, $mlScores['tfidf'], 'TFIDF score should be >= 0');
                $this->assertLessThanOrEqual(100, $mlScores['tfidf'], 'TFIDF score should be <= 100');
                $this->assertGreaterThanOrEqual(0, $mlScores['logreg'], 'LOGREG score should be >= 0');
                $this->assertLessThanOrEqual(100, $mlScores['logreg'], 'LOGREG score should be <= 100');

                // XAI message
                $matchMessage = $matcher->getMatchMessage(
                    $mlScores['rf'],
                    $featureMatches['skills']['matched'],
                    $featureMatches['skills']['missing'],
                    $opportunitySkills,
                    $student->course ?? null,
                    $student->academic_field ?? null,
                    $student->gpa ?? null,
                    $student->interests ? (is_array($student->interests) ? $student->interests : explode(',', $student->interests)) : null,
                    $student->year ?? null,
                    $student->university ?? null,
                    null,
                    $featureMatches
                );
                $this->assertIsString($matchMessage, 'Match message should be a string');

                fwrite(STDOUT, "XAI: \033[1;37m$matchMessage\033[0m\n");

                $allResults[] = [
                    'student' => $student,
                    'opportunity' => $opportunity,
                    'studentSkills' => $studentSkills,
                    'opportunitySkills' => $opportunitySkills,
                    'mlScores' => $mlScores,
                    'featureMatches' => $featureMatches,
                    'matchMessage' => $matchMessage,
                ];
                $pairCount++;
            }
        }
        // Assert at least one high match score exists
        $this->assertTrue($highScoreFound, 'No high (>=70) match score found in any model. High-match seed data may be missing.');
        // Render all results into a single PDF
        $pdfHtml = view('pdf.ml_test_report', [
            'allResults' => $allResults,
        ])->render();
        $pdf = Pdf::loadHTML($pdfHtml);
        $pdfFileName = base_path('ml_matching_test_report.pdf');
        $pdf->save($pdfFileName);
        fwrite(STDOUT, "\n\033[1;32mComprehensive PDF report generated: $pdfFileName\033[0m\n");
    }
}
