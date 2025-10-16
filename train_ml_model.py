#!/usr/bin/env python3
"""
ML Model Training Script for Interlink Platform
Trains a skill-based matching model for student-opportunity recommendations

Author: Njueini-Mary github
Purpose: Train and evaluate ML model, provide actionable feedback and improvement instructions
"""

import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier
import pickle
import json
from datetime import datetime
import os

print("=" * 70)
print("INTERLINK ML MODEL TRAINING SCRIPT")
print("Centralized Student Internship & Placement System")
print("=" * 70)
print()

# Step 1: Load Training Data
print("[Step 1] Loading training data...")
print("-" * 70)

try:
    # Check if CSV files exist
    if not os.path.exists('students.csv'):
        print("⚠️  students.csv not found. Generating synthetic data...")
        os.system('python3 ml_data_generator.py')
    
    students_df = pd.read_csv('students.csv')
    opportunities_df = pd.read_csv('opportunities.csv')
    
    print(f"✓ Loaded {len(students_df)} student profiles")
    print(f"✓ Loaded {len(opportunities_df)} opportunity listings")
    print()
except Exception as e:
    print(f"❌ Error loading data: {e}")
    print("Please run: python3 ml_data_generator.py first")
    exit(1)

# Step 2: Preprocess Data
print("[Step 2] Preprocessing data...")
print("-" * 70)

# Combine relevant text fields for better matching
students_df['combined_features'] = (
    students_df['skills'].fillna('') + ' ' +
    students_df['course'].fillna('') + ' ' +
    students_df['academic_field'].fillna('') + ' ' +
    students_df['interests'].fillna('')
)

opportunities_df['combined_features'] = (
    opportunities_df['skills'].fillna('') + ' ' +
    opportunities_df['title'].fillna('') + ' ' +
    opportunities_df['description'].fillna('')
)

print(f"✓ Combined features for {len(students_df)} students")
print(f"✓ Combined features for {len(opportunities_df)} opportunities")
print()

# Display sample features
print("Sample Student Features:")
print(students_df['combined_features'].iloc[0][:100] + "...")
print()
print("Sample Opportunity Features:")
print(opportunities_df['combined_features'].iloc[0][:100] + "...")
print()

# Step 3: Vectorization using TF-IDF
print("[Step 3] Vectorizing text features using TF-IDF...")
print("-" * 70)

# Initialize TF-IDF Vectorizer
vectorizer = TfidfVectorizer(
    max_features=100,  # Limit to top 100 features
    ngram_range=(1, 2),  # Use unigrams and bigrams
    stop_words='english',  # Remove common English words
    lowercase=True,
    min_df=2  # Minimum document frequency
)

# Fit vectorizer on all text data (students + opportunities)
all_text = pd.concat([
    students_df['combined_features'],
    opportunities_df['combined_features']
])

vectorizer.fit(all_text)

# Transform student and opportunity features
student_vectors = vectorizer.transform(students_df['combined_features'])
opportunity_vectors = vectorizer.transform(opportunities_df['combined_features'])

print(f"✓ Created {student_vectors.shape[1]} features per profile")
print(f"✓ Vectorized {student_vectors.shape[0]} student profiles")
print(f"✓ Vectorized {opportunity_vectors.shape[0]} opportunity profiles")
print()

# Display top features learned
feature_names = vectorizer.get_feature_names_out()
print("Top 20 Features Learned:")
print(", ".join(feature_names[:20]))
print()

# Step 3b: Prepare training data for supervised models
# For demonstration, create binary labels: 1 if student skills overlap with opportunity skills, else 0
def has_skill_overlap(student_skills, opp_skills):
    student_set = set(str(student_skills).lower().split(','))
    opp_set = set(str(opp_skills).lower().split(','))
    return int(len(student_set & opp_set) > 0)

X = []
y = []
for s_idx, s_row in students_df.iterrows():
    for o_idx, o_row in opportunities_df.iterrows():
        s_vec = student_vectors[s_idx].toarray()[0]
        o_vec = opportunity_vectors[o_idx].toarray()[0]
        X.append(np.concatenate([s_vec, o_vec]))
        y.append(has_skill_overlap(s_row['skills'], o_row['skills']))
X = np.array(X)
y = np.array(y)

# Step 3c: Train Logistic Regression
print("[Step 3c] Training Logistic Regression model...")
print("-" * 70)
logreg = LogisticRegression(max_iter=200)
logreg.fit(X, y)
logreg_score = logreg.score(X, y)
print(f"✓ Logistic Regression training accuracy: {logreg_score*100:.2f}%")

# Step 3d: Train Random Forest
print("[Step 3d] Training Random Forest model...")
print("-" * 70)
rf = RandomForestClassifier(n_estimators=50, random_state=42)
rf.fit(X, y)
rf_score = rf.score(X, y)
print(f"✓ Random Forest training accuracy: {rf_score*100:.2f}%")

# Step 4: Calculate Match Scores (for validation)
print("[Step 4] Calculating sample match scores...")
print("-" * 70)

# Calculate cosine similarity between all students and opportunities
similarity_matrix = cosine_similarity(student_vectors, opportunity_vectors)

# Convert to percentage scores
match_scores = (similarity_matrix * 100).round(2)

print(f"✓ Generated {similarity_matrix.shape[0] * similarity_matrix.shape[1]} match scores")
print()

# Show sample matches
print("Sample Matches (Top 5 for first student):")
student_idx = 0
student_name = students_df.iloc[student_idx]['name']
student_skills = students_df.iloc[student_idx]['skills']

print(f"\nStudent: {student_name}")
print(f"Skills: {student_skills}")
print(f"\nTop Matching Opportunities:")
print("-" * 70)

# Get top 5 matches for first student
top_indices = np.argsort(match_scores[student_idx])[::-1][:5]
for rank, idx in enumerate(top_indices, 1):
    opp_title = opportunities_df.iloc[idx]['title']
    opp_skills = opportunities_df.iloc[idx]['skills']
    score = match_scores[student_idx, idx]
    
    print(f"{rank}. {opp_title}")
    print(f"   This student profile has a {score}% match score for this opportunity. Scores above 60% are considered strong matches.")
    print(f"   Required Skills: {opp_skills}")
    print("To improve match scores, consider updating the training data or adjusting feature extraction parameters in this script.")
    print()

# Step 5: Save Trained Model
print("[Step 5] Saving trained model...")
print("-" * 70)

# Create models directory if it doesn't exist
os.makedirs('storage/ml_models', exist_ok=True)

# Save vectorizer
vectorizer_path = 'storage/ml_models/tfidf_vectorizer.pkl'
with open(vectorizer_path, 'wb') as f:
    pickle.dump(vectorizer, f)
print(f"✓ Saved TF-IDF vectorizer to {vectorizer_path}")

# Save Logistic Regression model
logreg_path = 'storage/ml_models/logreg_model.pkl'
with open(logreg_path, 'wb') as f:
    pickle.dump(logreg, f)
print(f"✓ Saved Logistic Regression model to {logreg_path}")

# Save Random Forest model
rf_path = 'storage/ml_models/rf_model.pkl'
with open(rf_path, 'wb') as f:
    pickle.dump(rf, f)
print(f"✓ Saved Random Forest model to {rf_path}")

# Save model metadata
vectorizer_params = vectorizer.get_params()
# Convert non-serializable types
for key, value in vectorizer_params.items():
    if isinstance(value, type):
        vectorizer_params[key] = str(value)

metadata = {
    'training_date': datetime.now().isoformat(),
    'num_students': len(students_df),
    'num_opportunities': len(opportunities_df),
    'num_features': len(feature_names),
    'top_features': feature_names[:50].tolist(),
    'vectorizer_params': vectorizer_params,
    'average_match_score': float(match_scores.mean()),
    'max_match_score': float(match_scores.max()),
    'min_match_score': float(match_scores.min())
}

metadata_path = 'storage/ml_models/model_metadata.json'
with open(metadata_path, 'w') as f:
    json.dump(metadata, f, indent=2)
print(f"✓ Saved model metadata to {metadata_path}")

# Save sample data for testing
sample_data = {
    'sample_student': {
        'name': student_name,
        'skills': student_skills,
        'features': students_df.iloc[0]['combined_features']
    },
    'sample_opportunities': [
        {
            'title': opportunities_df.iloc[idx]['title'],
            'skills': opportunities_df.iloc[idx]['skills'],
            'match_score': float(match_scores[0, idx])
        }
        for idx in top_indices[:5]
    ]
}

sample_path = 'storage/ml_models/sample_predictions.json'
with open(sample_path, 'w') as f:
    json.dump(sample_data, f, indent=2)
print(f"✓ Saved sample predictions to {sample_path}")

print()

# Step 6: Model Evaluation
print("[Step 6] Model Evaluation Metrics...")
print("-" * 70)

# Calculate statistics
high_matches = (match_scores >= 60).sum()
medium_matches = ((match_scores >= 40) & (match_scores < 60)).sum()
low_matches = (match_scores < 40).sum()
total_matches = match_scores.size

print(f"Match Score Distribution:")
print(f"  High Matches (≥60%):   {high_matches:,} ({high_matches/total_matches*100:.1f}%)")
print(f"  Medium Matches (40-60%): {medium_matches:,} ({medium_matches/total_matches*100:.1f}%)")
print(f"  Low Matches (<40%):    {low_matches:,} ({low_matches/total_matches*100:.1f}%)")
print()
print(f"Average match score across all pairs: {match_scores.mean():.2f}%")
print(f"Median match score:  {np.median(match_scores):.2f}%")
print(f"Standard deviation:  {match_scores.std():.2f}%")
print()
print("To achieve better results:")
print("- Update or expand the training data in students.csv and opportunities.csv")
print("- Adjust feature extraction parameters (e.g., n-gram range, stop words) in this script")
print("- Clean and normalize input data for consistency")
print()

# Step 7: Validation Test
print("[Step 7] Running validation tests...")
print("-" * 70)

# Test 1: Computer Science student should match tech opportunities
cs_student_idx = students_df[students_df['course'].str.contains('Computer', case=False, na=False)].index[0]
tech_opp_idx = opportunities_df[opportunities_df['title'].str.contains('Software|Developer|Tech', case=False, na=False)].index[0]

cs_tech_score = match_scores[cs_student_idx, tech_opp_idx]
print(f"Test 1: Computer Science student profile matched with a tech opportunity.")
print(f"  Match score: {cs_tech_score:.2f}% (Scores above 50% are considered strong matches)")
print(f"  Status: {'✓ PASS' if cs_tech_score > 50 else '✗ FAIL'}")
print()
# Test 2: Business student should match business opportunities
biz_student_idx = students_df[students_df['course'].str.contains('Business', case=False, na=False)].index[0]
biz_opp_idx = opportunities_df[opportunities_df['title'].str.contains('Marketing|Business|Sales', case=False, na=False)].index[0]
biz_score = match_scores[biz_student_idx, biz_opp_idx]
print(f"Test 2: Business student profile matched with a business opportunity.")
print(f"  Match score: {biz_score:.2f}% (Scores above 40% are considered relevant)")
print(f"  Status: {'✓ PASS' if biz_score > 40 else '✗ FAIL'}")
print()
print("To improve validation results, add more representative samples to your training data and tune thresholds in this script.")
print()

# Step 8: Generate Training Report
print("[Step 8] Generating training report...")
print("-" * 70)

report = f"""
INTERLINK ML MODEL TRAINING REPORT
{'=' * 70}

Training Date: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

DATASET SUMMARY
{'-' * 70}
- Total Students:       {len(students_df):,}
- Total Opportunities:  {len(opportunities_df):,}
- Total Features:       {len(feature_names):,}
- Match Pairs:          {total_matches:,}

MODEL CONFIGURATION
{'-' * 70}

ADDITIONAL MODELS
{'-' * 70}
- Logistic Regression:  Training accuracy: {logreg_score*100:.2f}%
- Random Forest:        Training accuracy: {rf_score*100:.2f}%

PERFORMANCE METRICS
{'-' * 70}
- Average Match Score:  {match_scores.mean():.2f}%
- Median Match Score:   {np.median(match_scores):.2f}%
- Standard Deviation:   {match_scores.std():.2f}%
- High Matches (≥60%):  {high_matches/total_matches*100:.1f}%

VALIDATION RESULTS
{'-' * 70}
- CS → Tech Match:      {cs_tech_score:.2f}% {'✓ PASS' if cs_tech_score > 50 else '✗ FAIL'}
- Business → Biz Match: {biz_score:.2f}% {'✓ PASS' if biz_score > 40 else '✗ FAIL'}

TOP 20 LEARNED FEATURES
{'-' * 70}
{', '.join(feature_names[:20])}

NEXT STEPS
{'-' * 70}
1. Integrate model with SkillMatcher service
2. Test with real user data
3. Monitor match accuracy in production
4. Retrain monthly with new data

{'=' * 70}
Model training completed successfully!
Report saved to: storage/ml_models/training_report.txt
"""

report_path = 'storage/ml_models/training_report.txt'
with open(report_path, 'w') as f:
    f.write(report)

print(report)
print(f"✓ Training report saved to {report_path}")
print()

# Final Summary
print("=" * 70)
print("✓ ML MODEL TRAINING COMPLETED SUCCESSFULLY!")
print("=" * 70)
print()
print("Files Created:")
print(f"  1. {vectorizer_path}")
print(f"  2. {metadata_path}")
print(f"  3. {sample_path}")
print(f"  4. {report_path}")
print()
print("Next Steps:")
print("  1. Review the training report")
print("  2. Test the model: python3 test_ml_model.py")
print("  3. Integrate with Laravel application")
print("  4. Monitor performance in production")
print()
print("Summary:")
print(f"- Training accuracy: {(high_matches/total_matches*100):.1f}% high matches")
print(f"- Average match score: {match_scores.mean():.1f}%")
print(f"- Features learned: {len(feature_names)}")
print()
print("To further improve your results:")
print("- Edit students.csv and opportunities.csv to add more training samples or clean data.")
print("- Adjust feature extraction parameters in this script.")
print("- Tune thresholds and validation logic for your use case.")
print("- Review your input data for consistency and relevance.")
print("Refer to train_ml_model.py and test_ml_model.py for further customization.")
print("=" * 70)
