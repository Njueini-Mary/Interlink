<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;




/**
 * SkillMatcher Service
 * 
 * Intelligent ML-based skill matching for student-opportunity pairing.
 * Uses TF-IDF vectorization and cosine similarity for accurate recommendations.
 * 
 * Purpose: Final Year BIT Project - Centralized Internship Management System
 * Innovation: ML-powered matching vs manual keyword search
 */
class SkillMatcher
{
    /**
     * Get matched and missing features (skills, course, year, university, academic_field, gpa, interests)
     * Returns: [ 'matched' => [...], 'missing' => [...] ] for each feature
     */
    public function getFeatureMatches(array $student, array $opportunity): array
    {
        $features = [
            'skills', 'course', 'year', 'university', 'academic_field', 'gpa', 'interests'
        ];
        $result = [];
        foreach ($features as $feature) {
            $studentVal = $student[$feature] ?? null;
            $oppVal = $opportunity[$feature] ?? null;
            if ($feature === 'skills' || $feature === 'interests') {
                $studentArr = is_array($studentVal) ? $studentVal : (is_string($studentVal) ? array_map('trim', explode(',', $studentVal)) : []);
                $oppArr = is_array($oppVal) ? $oppVal : (is_string($oppVal) ? array_map('trim', explode(',', $oppVal)) : []);
                $matched = array_values(array_intersect(array_map('strtolower', $oppArr), array_map('strtolower', $studentArr)));
                $missing = array_values(array_diff(array_map('strtolower', $oppArr), array_map('strtolower', $studentArr)));
                $result[$feature] = [
                    'matched' => $matched,
                    'missing' => $missing
                ];
            } else {
                $matched = ($studentVal && $oppVal && strtolower($studentVal) == strtolower($oppVal)) ? [$studentVal] : [];
                $missing = ($oppVal && (empty($matched))) ? [$oppVal] : [];
                $result[$feature] = [
                    'matched' => $matched,
                    'missing' => $missing
                ];
            }
        }
        return $result;
    }
    protected $modelPath;
    protected $metadataPath;
    protected $pythonCommand;
    protected $logregPath;
    protected $rfPath;

    public function __construct()
    {
        // Set paths to ML model files
        $this->modelPath = storage_path('ml_models/tfidf_vectorizer.pkl');
        $this->metadataPath = storage_path('ml_models/model_metadata.json');
        $this->logregPath = storage_path('ml_models/logreg_model.pkl');
        $this->rfPath = storage_path('ml_models/rf_model.pkl');
        
        // Force use of .venv Python executable for ML matching
        $venvPython = base_path('.venv/bin/python3');
        if (file_exists($venvPython)) {
            $this->pythonCommand = $venvPython;
        } else {
            $this->pythonCommand = $this->detectPythonCommand();
        }
        
        // Log initialization
        Log::info('SkillMatcher Service initialized', [
            'model_exists' => file_exists($this->modelPath),
            'python_command' => $this->pythonCommand
        ]);
    }

    /**
     * Detect available Python command
     */
    private function detectPythonCommand(): string
    {
        // Try python3 first (recommended)
        exec('which python3', $output, $return);
        if ($return === 0) {
            return 'python3';
        }
        
        // Fallback to python
        return 'python';
    }

    /**
     * Calculate match score using Python ML model
     * 
     * @param array $opportunitySkills Skills required by opportunity
     * @param array $studentSkills Skills possessed by student
     * @param string $studentProfile Additional student info (course, interests)
     * @param string $opportunityProfile Additional opportunity info (title, description)
     * @return float Match score (0-100)
     */
    public function getMatchPercentage(
        array $opportunitySkills, 
        array $studentSkills,
        string $studentProfile = '',
        string $opportunityProfile = '',
        array $studentFull = [],
        array $opportunityFull = []
    ): float
    {
        // Combine all features for ML input
        $studentText = implode(' ', $studentSkills) . ' ' . $studentProfile;
        $opportunityText = implode(' ', $opportunitySkills) . ' ' . $opportunityProfile;

        // If full arrays provided, add all features
        if (!empty($studentFull) && !empty($opportunityFull)) {
            $studentText .= ' ' . implode(' ', array_filter([
                $studentFull['course'] ?? '',
                $studentFull['year'] ?? '',
                $studentFull['university'] ?? '',
                $studentFull['academic_field'] ?? '',
                $studentFull['gpa'] ?? '',
                $studentFull['interests'] ?? '',
                $studentFull['activities'] ?? '',
                $studentFull['organizations'] ?? '',
                $studentFull['bio'] ?? '',
                $studentFull['resume'] ?? '',
                $studentFull['is_available'] ? 'available' : 'not available',
                $studentFull['rating'] ?? '',
                $studentFull['rating_count'] ?? ''
            ]));
            $opportunityText .= ' ' . implode(' ', array_filter([
                $opportunityFull['course'] ?? '',
                $opportunityFull['year'] ?? '',
                $opportunityFull['university'] ?? '',
                $opportunityFull['academic_field'] ?? '',
                $opportunityFull['min_gpa'] ?? '',
                $opportunityFull['interests'] ?? '',
                $opportunityFull['activities'] ?? '',
                $opportunityFull['organizations'] ?? '',
                $opportunityFull['description'] ?? '',
                $opportunityFull['industry'] ?? '',
                $opportunityFull['type'] ?? ''
            ]));
        }

        // If ML models exist, use them
        if (file_exists($this->modelPath) && file_exists($this->logregPath) && file_exists($this->rfPath)) {
            try {
                $scores = $this->getMLScores($studentText, $opportunityText);
                Log::debug('ML Match Scores Calculated', [
                    'tfidf' => $scores['tfidf'],
                    'logreg' => $scores['logreg'],
                    'rf' => $scores['rf'],
                    'student_skills' => count($studentSkills),
                    'opportunity_skills' => count($opportunitySkills)
                ]);
                // Use TFIDF as the main score
                return $scores['tfidf'];
            } catch (\Exception $e) {
                Log::warning('ML calculation failed, using fallback', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        // Fallback to simple percentage calculation
        return $this->calculateSimpleMatch($opportunitySkills, $studentSkills);
    }

    /**
     * Calculate match using Python ML models via subprocess
     * Returns array: ['tfidf' => float, 'logreg' => float, 'rf' => float]
     */
    public function getMLScores(string $studentText, string $opportunityText): array
    {
        $pythonScript = base_path('calculate_match.py');
        
        // Escape arguments for shell
        $student = escapeshellarg($studentText);
        $opportunity = escapeshellarg($opportunityText);
        $vectorizer = escapeshellarg($this->modelPath);
        $logreg = escapeshellarg($this->logregPath);
        $rf = escapeshellarg($this->rfPath);
        
        // Execute Python script
        $command = "{$this->pythonCommand} {$pythonScript} {$student} {$opportunity} {$vectorizer} {$logreg} {$rf}";
        $output = shell_exec($command);
        
        $scores = explode(',', trim($output));
        
        return [
            'tfidf' => isset($scores[0]) ? floatval($scores[0]) : 0.0,
            'logreg' => isset($scores[1]) ? floatval($scores[1]) : 0.0,
            'rf' => isset($scores[2]) ? floatval($scores[2]) : 0.0,
        ];
    }

    /**
     * Create Python script for match calculation
     */
    private function createMatchScript(string $path): void
    {
        $script = <<<'PYTHON'
#!/usr/bin/env python3
import sys
import pickle
from sklearn.metrics.pairwise import cosine_similarity

try:
    student_text = sys.argv[1]
    opportunity_text = sys.argv[2]
    model_path = sys.argv[3]
    
    # Load vectorizer
    with open(model_path, 'rb') as f:
        vectorizer = pickle.load(f)
    
    # Transform texts
    student_vec = vectorizer.transform([student_text])
    opp_vec = vectorizer.transform([opportunity_text])
    
    # Calculate similarity
    similarity = cosine_similarity(student_vec, opp_vec)[0][0]
    
    # Convert to percentage
    score = similarity * 100
    
    print(round(score, 2))
    
except Exception as e:
    print(0)  # Return 0 on error
PYTHON;
        
        file_put_contents($path, $script);
        chmod($path, 0755);
    }

    /**
     * Simple fallback matching based on skill overlap
     */
    private function calculateSimpleMatch(array $opportunitySkills, array $studentSkills): float
    {
        if (empty($opportunitySkills)) {
            return 0;
        }
        
        // Calculate direct skill overlap
        $matchedSkills = array_intersect(
            array_map('strtolower', $opportunitySkills),
            array_map('strtolower', $studentSkills)
        );
        
        $matchCount = count($matchedSkills);
        $totalRequired = count($opportunitySkills);
        
        // Calculate percentage
        $percentage = ($matchCount / $totalRequired) * 100;
        
        // Bonus for having extra skills (up to 10%)
        $extraSkills = count($studentSkills) - $matchCount;
        $bonus = min(10, $extraSkills * 2);
        
        return min(100, $percentage + $bonus);
    }

    /**
     * Predict binary match (1 = match, 0 = no match)
     * Based on threshold matching
     */
    public function predictMatch(array $opportunitySkills, array $studentSkills, string $studentProfile = '', string $opportunityProfile = ''): int
    {
        $matchPercentage = $this->getMatchPercentage(
            $opportunitySkills, 
            $studentSkills,
            $studentProfile,
            $opportunityProfile
        );
        
        // Threshold: 40% or higher is considered a match
        return $matchPercentage >= 40 ? 1 : 0;
    }

    /**
     * Get matched skills between opportunity and student
     */
    public function getMatchedSkills(array $opportunitySkills, array $studentSkills): array
    {
        // Map lowercase to original for display
        $oppLowerMap = [];
        foreach ($opportunitySkills as $skill) {
            $oppLowerMap[strtolower($skill)] = $skill;
        }
        $studentLower = array_map('strtolower', $studentSkills);
        $oppLower = array_map('strtolower', $opportunitySkills);
        $matchedLower = array_intersect($oppLower, $studentLower);
        // Return original skill names for display
        return array_values(array_map(function($s) use ($oppLowerMap) {
            return $oppLowerMap[$s];
        }, $matchedLower));
    }

    /**
     * Get missing skills (required but not possessed)
     */
    public function getMissingSkills(array $opportunitySkills, array $studentSkills): array
    {
        // Map lowercase to original for display
        $oppLowerMap = [];
        foreach ($opportunitySkills as $skill) {
            $oppLowerMap[strtolower($skill)] = $skill;
        }
        $studentLower = array_map('strtolower', $studentSkills);
        $oppLower = array_map('strtolower', $opportunitySkills);
        $missingLower = array_diff($oppLower, $studentLower);
        // Return original skill names for display
        return array_values(array_map(function($s) use ($oppLowerMap) {
            return $oppLowerMap[$s];
        }, $missingLower));
    }

    /**
     * Get match category based on score and skill overlap
     * @param float $matchScore Match percentage (0-100)
     * @param array $matchedSkills
     * @param array $requiredSkills
     * @return string Category: 'excellent', 'good', 'fair', 'poor'
     */
    public function getMatchCategory(float $matchScore, array $matchedSkills = [], array $requiredSkills = []): string
    {
        $matchedCount = is_array($matchedSkills) ? count($matchedSkills) : 0;
        $requiredCount = is_array($requiredSkills) ? count($requiredSkills) : 0;
        $skillRatio = $requiredCount > 0 ? ($matchedCount / $requiredCount) : 0;

        // If most required skills are matched, boost category
        if ($skillRatio >= 0.8) return 'excellent';
        if ($skillRatio >= 0.6) return 'good';
        if ($skillRatio >= 0.4) return 'fair';

        // Otherwise, fallback to ML score
        if ($matchScore >= 80) return 'excellent';
        if ($matchScore >= 60) return 'good';
        if ($matchScore >= 40) return 'fair';
        return 'poor';
    }

    /**
     * Get match message for display (dynamic, considers skills)
     * @param float $matchScore
     * @param array $matchedSkills
     * @param array $missingSkills
     * @param array $requiredSkills
     * @return string
     */
    public function getMatchMessage(
        float $matchScore,
        array $matchedSkills = [],
        array $missingSkills = [],
        array $requiredSkills = [],
        $course = null,
        $academicField = null,
        $gpa = null,
        $interests = null,
        $year = null,
        $university = null,
        $viewerRole = null,
        array $featureMatches = []
    ): string {
        $category = $this->getMatchCategory($matchScore, $matchedSkills, $requiredSkills);
        $profileFactors = [];
        if ($course) $profileFactors[] = $course;
        if ($academicField) $profileFactors[] = $academicField;
        if ($gpa) $profileFactors[] = $gpa;
        if ($year) $profileFactors[] = $year;
        if ($university) $profileFactors[] = $university;
        if ($interests && is_array($interests) && count($interests)) $profileFactors = array_merge($profileFactors, $interests);
        $profileSummary = count($profileFactors) ? implode(', ', $profileFactors) : '';

        // Build feature-by-feature summary
        $featureSummary = [];
        foreach ([
            'skills' => 'Skills',
            'course' => 'Course',
            'year' => 'Year',
            'university' => 'University',
            'academic_field' => 'Academic Field',
            'gpa' => 'GPA',
            'interests' => 'Interests',
        ] as $key => $label) {
            if (isset($featureMatches[$key])) {
                $m = $featureMatches[$key]['matched'];
                $miss = $featureMatches[$key]['missing'];
                if (count($m) && !count($miss)) {
                    $featureSummary[] = "$label matched: " . implode(', ', $m);
                } elseif (count($m) && count($miss)) {
                    $featureSummary[] = "$label partial match: " . implode(', ', $m) . ". Missing: " . implode(', ', $miss);
                } elseif (count($miss)) {
                    $featureSummary[] = "$label missing: " . implode(', ', $miss);
                }
            }
        }
        $featureSummaryStr = implode('; ', $featureSummary);

        // User-friendly message
        // Diverse, score-aligned messages
        if ($matchScore >= 80) {
            $catMsg = [
                'Outstanding match! This applicant is a top fit for the opportunity.',
                'Excellent fit! All key requirements are met.',
                'Superb candidate—profile aligns perfectly with the opportunity.'
            ];
        } elseif ($matchScore >= 60) {
            $catMsg = [
                'Strong candidate for this role.',
                'Good match—most requirements are met.',
                'This applicant brings valuable strengths to the opportunity.'
            ];
        } elseif ($matchScore >= 40) {
            $catMsg = [
                'Decent fit—some requirements are met.',
                'Moderate match. The applicant covers several important areas.',
                'This applicant could contribute, but may need to develop further.'
            ];
        } else {
            $catMsg = [
                'Low match. The applicant may need to improve their profile.',
                'Few requirements met—consider upskilling for a better fit.',
                'This applicant is not a strong match for this opportunity.'
            ];
        }
        $message = $catMsg[array_rand($catMsg)];
        if ($featureSummaryStr) {
            $message .= ' ' . $featureSummaryStr . '.';
        }
        if ($profileSummary) {
            $message .= ' Profile: ' . $profileSummary . '.';
        }
        return $message;
    }

    // Backward compatibility: if only score is provided
    public function getMatchMessageSimple(float $matchScore): string
    {
        return $this->getMatchMessage($matchScore, [], []);
    }

    /**
     * Batch match a student against multiple opportunities
     * 
     * @param \App\Models\Student $student
     * @param \Illuminate\Database\Eloquent\Collection $opportunities
     * @return array Sorted array of matches with scores
     */
    public function batchMatch($student, $opportunities): array
    {
        $matches = [];
        $studentSkills = $student->skills->pluck('name')->toArray();
        $studentProfile = $student->course . ' ' . ($student->academic_field ?? '') . ' ' . ($student->interests ?? '');
        
        foreach ($opportunities as $opportunity) {
            $opportunitySkills = $opportunity->skills->pluck('name')->toArray();
            $opportunityProfile = $opportunity->title . ' ' . $opportunity->description;
            
            $matchScore = $this->getMatchPercentage(
                $opportunitySkills,
                $studentSkills,
                $studentProfile,
                $opportunityProfile
            );
            
            // Only include if prediction is positive
            if ($this->predictMatch($opportunitySkills, $studentSkills, $studentProfile, $opportunityProfile) === 1) {
                $matches[] = [
                    'opportunity' => $opportunity,
                    'match_score' => round($matchScore, 2),
                    'match_category' => $this->getMatchCategory($matchScore),
                    'match_message' => $this->getMatchMessage($matchScore),
                    'matched_skills' => $this->getMatchedSkills($opportunitySkills, $studentSkills),
                    'missing_skills' => $this->getMissingSkills($opportunitySkills, $studentSkills)
                ];
            }
        }
        
        // Sort by match score (descending)
        usort($matches, function($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });
        
        return $matches;
    }

    /**
     * Check if ML model is available
     */
    public function isMLModelAvailable(): bool
    {
        return file_exists($this->modelPath);
    }

    /**
     * Get ML model metadata
     */
    public function getModelMetadata(): ?array
    {
        if (!file_exists($this->metadataPath)) {
            return null;
        }
        
        return json_decode(file_get_contents($this->metadataPath), true);
    }
}