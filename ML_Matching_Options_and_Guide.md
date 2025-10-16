# ML Matching Options & Implementation Guide for Interlink Platform

## My Options for ML Data Preparation & Matching

### 1. Using Existing Public Datasets
- I could start quickly and benchmark with large datasets, but they often don’t match my codebase fields and need heavy preprocessing.

### 2. Generating Synthetic Data Using Python Scripts (**My Choice**)
- I have full control over the schema, so my data matches my models and fields exactly. It’s scalable, realistic, and easy to update.
- This option lets me simulate diverse scenarios and rapidly prototype and test my ML logic.
- When I have enough real data, I’ll retrain or fine-tune my ML models.

### 3. Using Real Data from My Platform
- This would be perfect for production, but my data is limited right now. I’ll use it to improve my models as my platform grows.

### 4. Hybrid Approach
- I can combine synthetic and real data, or enrich with public datasets, but it requires careful mapping and validation.

## Why I Chose Python Script Generation
- My codebase has unique models and fields (Student, Opportunity, Organization, Skill, etc.).
- Synthetic data ensures perfect alignment and allows me to rapidly prototype and test.
- I can easily scale, update, and simulate diverse scenarios.
- Once I have more real data, I’ll retrain or fine-tune my ML models.

---

## Step-by-Step Implementation Guide

### 1. Define My Schema (Fields from My Codebase)

#### Student Model
- name, email, skills, course, year, university, academic_field, bio, interests, gpa, is_available, rating, rating_count, resume, activities, organizations

#### Opportunity Model
- title, skills, type, description, location, organization, activities, applications_count, created_at

#### Organization Model
- name, location, logo, students, users, opportunities

#### Skill Model
- name

### 2. Generate Synthetic Data with Python

#### My Full Python Script (well-commented)
```python
from faker import Faker
import random
import pandas as pd

fake = Faker()

# Define skills and courses
skills = ['Python', 'Data Science', 'Web Dev', 'AI', 'Marketing', 'Finance', 'Design', 'Project Management']
courses = ['Computer Science', 'Business', 'Engineering', 'Design', 'Finance', 'Marketing']
organizations = [fake.company() for _ in range(20)]

# Generate Students
students = []
for _ in range(500):
    students.append({
        'name': fake.name(),
        'email': fake.email(),
        'skills': ', '.join(random.sample(skills, k=random.randint(2, 5))),
        'course': random.choice(courses),
        'year': random.randint(1, 4),
        'university': fake.company(),
        'academic_field': random.choice(courses),
        'bio': fake.sentence(),
        'interests': ', '.join(random.sample(skills, k=3)),
        'gpa': round(random.uniform(2.0, 4.0), 2),
        'is_available': random.choice([True, False]),
        'rating': round(random.uniform(3.0, 5.0), 2),
        'rating_count': random.randint(1, 50),
        'resume': fake.file_name(extension='pdf'),
        'activities': fake.sentence(),
        'organizations': ', '.join(random.sample(organizations, k=2))
    })

# Generate Opportunities
opportunities = []
for _ in range(200):
    org = random.choice(organizations)
    opportunities.append({
        'title': fake.job(),
        'skills': ', '.join(random.sample(skills, k=3)),
        'type': random.choice(['Internship', 'Attachment', 'Volunteering']),
        'description': fake.text(max_nb_chars=200),
        'location': fake.city(),
        'organization': org,
        'activities': fake.sentence(),
        'applications_count': random.randint(0, 100),
        'created_at': fake.date_this_year().isoformat()
    })

# Generate Organizations
orgs = []
for org in organizations:
    orgs.append({
        'name': org,
        'location': fake.city(),
        'logo': fake.file_name(extension='png'),
        'students': ', '.join([fake.name() for _ in range(random.randint(2, 10))]),
        'users': ', '.join([fake.name() for _ in range(random.randint(1, 5))]),
        'opportunities': ', '.join([fake.job() for _ in range(random.randint(1, 5))])
    })

# Generate Skills
skills_data = [{'name': skill} for skill in skills]

# Save to CSV
pd.DataFrame(students).to_csv('students.csv', index=False)
pd.DataFrame(opportunities).to_csv('opportunities.csv', index=False)
pd.DataFrame(orgs).to_csv('organizations.csv', index=False)
pd.DataFrame(skills_data).to_csv('skills.csv', index=False)
```

### 3. Load Data in Google Colab
- I upload the generated CSV files to Colab.
- Example:
```python
import pandas as pd
students = pd.read_csv('students.csv')
opportunities = pd.read_csv('opportunities.csv')
organizations = pd.read_csv('organizations.csv')
skills = pd.read_csv('skills.csv')
```

### 4. Feature Engineering & ML Matching
- I vectorize skills, interests, and other text fields (TF-IDF, embeddings).
- I use cosine similarity or nearest neighbors for matching.
- Example (Colab):
```python
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Vectorize student and opportunity skills
vectorizer = TfidfVectorizer()
student_skill_matrix = vectorizer.fit_transform(students['skills'])
opportunity_skill_matrix = vectorizer.transform(opportunities['skills'])

# Compute similarity
similarity_scores = cosine_similarity(student_skill_matrix, opportunity_skill_matrix)
```

### 5. Build ML Service Backend
- I deploy my matching logic as a Flask or FastAPI microservice.
- Example (Flask):
```python
from flask import Flask, request, jsonify
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

app = Flask(__name__)
students = pd.read_csv('students.csv')
opportunities = pd.read_csv('opportunities.csv')
vectorizer = TfidfVectorizer().fit(students['skills'].tolist() + opportunities['skills'].tolist())

@app.route('/match', methods=['POST'])
def match():
    student_id = request.json['student_id']
    student = students.iloc[student_id]
    student_vec = vectorizer.transform([student['skills']])
    opportunity_vecs = vectorizer.transform(opportunities['skills'])
    scores = cosine_similarity(student_vec, opportunity_vecs)[0]
    top_indices = scores.argsort()[-5:][::-1]
    matches = opportunities.iloc[top_indices].to_dict(orient='records')
    return jsonify({'matches': matches, 'scores': scores[top_indices].tolist()})

if __name__ == '__main__':
    app.run(debug=True)
```

### 6. Integrate with Laravel
- I use Laravel's HTTP client (Guzzle) to send student/opportunity data to the ML service and receive match scores.
- Example:
```php
// In my controller
$response = Http::post('http://ml-service-url/match', [
    'student_id' => $studentId
]);
$matches = $response->json()['matches'];
```

### 7. Extend to Other Models
- To match organizations, students, or other entities, I simply adjust the feature extraction and API endpoints in my ML service.
- I keep my Python scripts modular and reusable for new models.

---

## ML Model Training & Comparison: TF-IDF, Logistic Regression, Random Forest

### Training & Testing Workflow

1. **Feature Extraction**
   - I use TF-IDF to vectorize student and opportunity profiles (skills, courses, interests, etc.).

2. **Matching Algorithms**
   - **TF-IDF + Cosine Similarity**: Calculates similarity scores between student and opportunity vectors.
   - **Logistic Regression**: Trained to predict match probability based on concatenated student/opportunity vectors and skill overlap.
   - **Random Forest**: Trained similarly for comparison, often more robust to feature interactions.

3. **Training Commands**
   - Run: `python3 train_ml_model.py`
   - Output: Trains and saves all three models (TF-IDF, Logistic Regression, Random Forest) in `storage/ml_models/`.
   - Example output:
     - TF-IDF: Top features learned, match score distribution
     - Logistic Regression: Training accuracy (e.g., 85%)
     - Random Forest: Training accuracy (e.g., 90%)

4. **Testing & Model Comparison**
   - Run: `python3 test_ml_model.py`
   - Output: For each test scenario, shows match scores from all three models for direct comparison.
   - Example output:
     - Cosine Similarity: 59.7%
     - Logistic Regression: 82.1%
     - Random Forest: 88.5%
   - Use these results to select the best model for your use case.

5. **How to Interpret Results**
   - Cosine Similarity: Measures text-based similarity, good for baseline matching.
   - Logistic Regression: Predicts probability of a match, interpretable and fast.
   - Random Forest: Predicts probability, can capture complex feature interactions, often higher accuracy.
   - Compare accuracy and match probabilities to choose the best model for production.

6. **Next Steps for Tuning**
   - Add more representative samples to `students.csv` and `opportunities.csv`.
   - Adjust feature extraction parameters (e.g., n-gram range, stop words) in `train_ml_model.py`.
   - Experiment with other ML algorithms if needed.
   - Monitor model performance and retrain as new data becomes available.

---

## Example Command Outputs (for Defense)

```
$ python3 train_ml_model.py
[Step 3c] Training Logistic Regression model...
✓ Logistic Regression training accuracy: 85.12%
[Step 3d] Training Random Forest model...
✓ Random Forest training accuracy: 90.34%
```

```
$ python3 test_ml_model.py
Software Developer Student:
Cosine Similarity: 59.7%
Logistic Regression: 82.1%
Random Forest: 88.5%
```

---

## Recommendation
- Use Random Forest for best accuracy, but Logistic Regression is faster and more interpretable.
- Always compare with TF-IDF baseline to validate improvements.
- Document all outputs for your defense and ongoing monitoring.

---
