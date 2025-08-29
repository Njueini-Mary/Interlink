<?php

namespace App\Services;

use Phpml\Classification\SVC;

class SkillMatcher
{
    protected $model;

    public function __construct()
    {
        // Initialize ML model with error handling
        try {
            $modelPath = storage_path('app/ml/skill_model.dat');
            
            // Verify model file exists
            if (!file_exists($modelPath)) {
                throw new \Exception("ML model file not found at: {$modelPath}");
            }
            
            $this->model = unserialize(file_get_contents($modelPath));
            logger()->info('ML model loaded successfully');

        } catch (\Exception $e) {
            // Log error and fallback to basic matching
            logger()->error('ML Model Error: '.$e->getMessage());
            $this->model = null;
        }
    }

    /**
     * Predict match using ML model with fallback to simple skill count
     * 
     * @param array $jobSkills Skills required by the job
     * @param array $freelancerSkills Skills possessed by freelancer
     * @return int 1 for match, 0 for no match
     */
    public function predictMatch(array $jobSkills, array $freelancerSkills): int
    {
        // Fallback if model failed to load
        if (!$this->model) {
            $matchCount = count(array_intersect($jobSkills, $freelancerSkills));
            logger()->warning('Using fallback matching', [
                'job_skills' => $jobSkills,
                'freelancer_skills' => $freelancerSkills,
                'match_count' => $matchCount
            ]);
            return $matchCount > 0 ? 1 : 0;
        }

        // Prepare feature for ML model (skill overlap count)
        $overlap = count(array_intersect($jobSkills, $freelancerSkills));
        
        // Log prediction inputs
        logger()->debug('ML Prediction Input', [
            'job_skills' => $jobSkills,
            'freelancer_skills' => $freelancerSkills,
            'overlap_count' => $overlap
        ]);

        // Get prediction from ML model
        $prediction = $this->model->predict([[ $overlap ]])[0];
        
        // Log prediction result
        logger()->debug('ML Prediction Result', [
            'prediction' => $prediction,
            'overlap_count' => $overlap
        ]);

        return $prediction;
    }

    /**
     * Calculate match percentage for display purposes
     * 
     * @param array $jobSkills Skills required by the job
     * @param array $freelancerSkills Skills possessed by freelancer
     * @return float Percentage match (0-100)
     */
    public function getMatchPercentage(array $jobSkills, array $freelancerSkills): float
    {
        $totalRequired = count($jobSkills);
        $overlap = count(array_intersect($jobSkills, $freelancerSkills));
        
        // Prevent division by zero
        if ($totalRequired === 0) return 0;
        
        return ($overlap / $totalRequired) * 100;
    }
}