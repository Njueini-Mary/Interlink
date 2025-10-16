<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phpml\Preprocessing\Normalizer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;

class MLController extends Controller
{
    /**
     * Match a student to suitable opportunities using ML-based skill matching.
     * 
     * This method analyzes the student's profile (skills, academic field, interests)
     * and finds the most relevant opportunities using the SkillMatcher service.
     * 
     * Expected JSON payload fields:
     * - student_id (uuid): The ID of the student to match
     * 
     * Returns:
     * - JSON array of matched opportunities with match scores
     * 
     * @param Request $request The incoming HTTP request containing student_id
     * @return \Illuminate\Http\JsonResponse JSON response with matched opportunities
     */
    public function matchStudent(Request $request)
    {
        // Validate the incoming request to ensure student_id is provided
        $request->validate([
            'student_id' => 'required|uuid|exists:students,id',
        ]);

        try {
            // Retrieve the student with their skills relationship
            $student = \App\Models\Student::with('skills')->findOrFail($request->input('student_id'));
            
            // Get all open opportunities with their required skills
            $opportunities = \App\Models\Opportunity::with('skills')
                ->where('status', 'open')
                ->get();

            // Initialize the skill matcher service for ML-based matching
            $matcher = new \App\Services\SkillMatcher();
            
            // Array to store opportunities with their match scores
            $matchedOpportunities = [];
            
            // Extract student skills as an array of skill names
            $studentSkills = $student->skills->pluck('name')->toArray();

            // Iterate through each opportunity and calculate match score
            foreach ($opportunities as $opportunity) {
                // Extract required skills for the opportunity
                $opportunitySkills = $opportunity->skills->pluck('name')->toArray();
                
                // Calculate match percentage using the ML matcher
                $matchPercentage = $matcher->getMatchPercentage($opportunitySkills, $studentSkills);
                
                // Get binary prediction (1 for match, 0 for no match)
                $prediction = $matcher->predictMatch($opportunitySkills, $studentSkills);
                
                // Only include opportunities with prediction = 1 (match)
                if ($prediction === 1) {
                    $matchedOpportunities[] = [
                        'opportunity' => $opportunity,
                        'match_percentage' => round($matchPercentage, 2),
                        'matched_skills' => array_intersect($studentSkills, $opportunitySkills),
                    ];
                }
            }

            // Sort opportunities by match percentage in descending order
            usort($matchedOpportunities, function($a, $b) {
                return $b['match_percentage'] <=> $a['match_percentage'];
            });

            // Return the top 10 matched opportunities
            return response()->json([
                'success' => true,
                'student_id' => $student->id,
                'matches' => array_slice($matchedOpportunities, 0, 10),
                'total_matches' => count($matchedOpportunities),
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Illuminate\Support\Facades\Log::error('ML Student Matching Error: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while matching opportunities.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
