<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phpml\Preprocessing\Normalizer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;

class MLController extends Controller
{
    /**
     * Predict whether a freelancer-job pair is a good match.
     * 
     * Expected JSON payload fields:
     * - job_category (string)
     * - freelancer_skills (string, skills separated by pipe " | ")
     * - experience_level (numeric)
     * - client_rating (numeric)
     * - job_budget (numeric)
     * - job_deadline_days (numeric)
     */
    public function matchFreelancer(Request $request)
    {
        $request->validate([
            'job_category'       => 'required|string',
            'freelancer_skills'  => 'required|string',
            'experience_level'   => 'required|numeric',
            'client_rating'      => 'required|numeric',
            'job_budget'         => 'required|numeric',
            'job_deadline_days'  => 'required|numeric',
        ]);

        $modelFile = storage_path('app/ml/matching_model.json');
        if (!file_exists($modelFile)) {
            return response()->json(['error' => 'ML model not found. Please train the model first.'], 500);
        }
        $modelData = json_decode(file_get_contents($modelFile), true);
        $classifier    = unserialize($modelData['classifier']);
        $vocabulary    = $modelData['vocabulary'];
        $jobCategories = $modelData['jobCategories'];

        // One-hot encode job_category using the known vocabulary.
        $onehot = [];
        foreach ($jobCategories as $cat) {
            $onehot[] = ($request->input('job_category') === $cat) ? 1.0 : 0.0;
        }

        // Process freelancer_skills: convert from pipe-separated to space-separated.
        $skillsStr = implode(' ', explode('|', $request->input('freelancer_skills')));
        $vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
        // Set fixed vocabulary from the training stage.
        $vectorizer->setVocabulary($vocabulary);
        $skillsArray = [$skillsStr];
        $vectorizer->transform($skillsArray);
        $skillsVector = [];
        // Build a fixed-length vector from the vocabulary order.
        foreach ($vocabulary as $word => $dummy) {
            $skillsVector[] = isset($skillsArray[0][$word]) ? (float)$skillsArray[0][$word] : 0.0;
        }

        // Numerical features:
        $numerical = [
            (float)$request->input('experience_level'),
            (float)$request->input('client_rating'),
            (float)$request->input('job_budget'),
            (float)$request->input('job_deadline_days'),
        ];

        // Merge all features.
        $sample = array_merge($onehot, $skillsVector, $numerical);

        // Normalize the sample.
        $normalizer = new Normalizer();
        // For consistency, we fit the normalizer on the sample itself.
        $normalizer->fit([$sample]);
        $normalizer->transform([$sample]);
        $sample = $sample[0];

        // Predict.
        $prediction = $classifier->predict([$sample]);

        return response()->json([
            'match' => $prediction[0]  // 1 indicates a good match, 0 indicates poor match.
        ]);
    }
}
