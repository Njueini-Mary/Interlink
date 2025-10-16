# Complete Setup & Testing Guide
## Interlink ML Matching System - Final Year Project

**For:** Final Year BIT Student  
**Purpose:** Step-by-step guide to train model, test features, and prepare for defense

---

## Part 1: ML Model Training & Setup

### Step 1: Install Python Dependencies

```bash
# Navigate to your project directory
cd /home/me12free/IS/Interlink

# Install required Python packages
pip3 install pandas numpy scikit-learn faker

# Verify installation
python3 -c "import pandas, numpy, sklearn; print('✓ All packages installed')"
```

### Step 2: Generate Training Data

```bash
# Run the data generator
python3 ml_data_generator.py

# Expected output:
# - students.csv (500 student profiles)
# - opportunities.csv (200 opportunities)
# - organizations.csv (20 organizations)
# - skills.csv (skill taxonomy)
```

**Verify:** Check that CSV files exist with:
```bash
ls -lh *.csv
```

### Step 3: Train the ML Model

```bash
# Run training script
python3 train_ml_model.py

# Expected output:
# ✓ Model trained successfully
# ✓ Training report generated
# ✓ Model saved to storage/ml_models/
```

**Important:** This creates:
- `storage/ml_models/tfidf_vectorizer.pkl` (the trained model)
- `storage/ml_models/model_metadata.json` (model information)
- `storage/ml_models/training_report.txt` (for your defense)

### Step 4: Test the ML Model

```bash
# Run test script
python3 test_ml_model.py

# Expected output:
# ✓ Test success rate: 80%+ 
# ✓ High matches avg: 60%+
# ✓ All test cases passed
```

**Save this output!** You'll use these metrics in your defense presentation.

---

## Part 2: Laravel Integration

### Step 5: Update Laravel Configuration

```bash
# Ensure storage directory exists
mkdir -p storage/ml_models

# Set correct permissions
chmod -R 775 storage
chown -R www-data:www-data storage  # Adjust for your server
```

### Step 6: Clear Laravel Cache

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
```

### Step 7: Test ML Integration in Laravel

Create a test route to verify ML matching works:

```php
// In routes/web.php, add:
Route::get('/test-ml', function() {
    $matcher = new \App\Services\SkillMatcher();
    
    return [
        'ml_available' => $matcher->isMLModelAvailable(),
        'metadata' => $matcher->getModelMetadata(),
        'test_match' => $matcher->getMatchPercentage(
            ['Python', 'Web Development'],
            ['Python', 'JavaScript']
        )
    ];
});
```

Visit: `http://localhost/test-ml` and verify JSON response.

---

## Part 3: Testing ML Features

### Test 1: Student Dashboard ML Recommendations

1. **Login as a student:**
   - Email: student@example.com
   - Password: password

2. **Navigate to Dashboard**
   - URL: `/student/dashboard` or `/dashboard`

3. **Expected Results:**
   - Section titled "Recommended for You" or "Top Matches"
   - List of 5 opportunities with match scores
   - Match percentages displayed (e.g., "85% match")
   - Matched skills highlighted

4. **Screenshot this for your defense!**

### Test 2: Opportunity Browsing with ML Sorting

1. **Go to Opportunities page:**
   - URL: `/opportunities`

2. **Use filters:**
   - Try "Sort by: Best Match"
   - Filter by category/type

3. **Expected Results:**
   - Opportunities sorted by relevance to student profile
   - Match scores visible on each card
   - "Why this matches you" tooltip/message

### Test 3: Application Process with ML Suggestions

1. **Click "Apply" on any opportunity**

2. **Expected Results:**
   - Match score displayed before application
   - Message like "Great match! You meet 80% of requirements"
   - Warning if low match (<40%)

3. **Submit application and verify**

### Test 4: Organization View - ML Candidate Matching

1. **Login as organization:**
   - Create organization account OR use seed data

2. **Post an opportunity** (if none exist)

3. **View applications:**
   - Navigate to opportunity details
   - Click "View Applications"

4. **Expected Results:**
   - Applicants sorted by match score
   - "Top Match" badge on best candidates
   - Match percentage for each applicant
   - Matched skills vs required skills comparison

---

## Part 4: Creating Test Data for Defense Demo

### Option A: Use Seed Data

```bash
# Create database seeder
php artisan make:seeder MLDemoSeeder

# Add to database/seeders/MLDemoSeeder.php:
```

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Organization;
use App\Models\Opportunity;
use App\Models\Skill;

class MLDemoSeeder extends Seeder
{
    public function run()
    {
        // Create demo skills
        $skills = [
            'Python', 'Java', 'JavaScript', 'Web Development',
            'Data Science', 'Machine Learning', 'Marketing',
            'Business', 'Design', 'Project Management'
        ];
        
        foreach ($skills as $skillName) {
            Skill::firstOrCreate(['name' => $skillName]);
        }
        
        // Create demo student
        $studentUser = User::create([
            'name' => 'Demo Student',
            'email' => 'demo.student@interlink.com',
            'password' => bcrypt('demo123'),
            'role' => 'student'
        ]);
        
        $student = Student::create([
            'user_id' => $studentUser->id,
            'course' => 'Computer Science',
            'academic_field' => 'Software Engineering',
            'year' => 3,
            'university' => 'Demo University',
            'interests' => 'Web Development, AI, Startups'
        ]);
        
        // Attach skills
        $pythonSkill = Skill::where('name', 'Python')->first();
        $jsSkill = Skill::where('name', 'JavaScript')->first();
        $webDevSkill = Skill::where('name', 'Web Development')->first();
        
        $student->skills()->attach([$pythonSkill->id, $jsSkill->id, $webDevSkill->id]);
        
        // Create demo organization and opportunities
        $orgUser = User::create([
            'name' => 'Tech Corp',
            'email' => 'techcorp@interlink.com',
            'password' => bcrypt('demo123'),
            'role' => 'organization'
        ]);
        
        $org = Organization::create([
            'user_id' => $orgUser->id,
            'name' => 'Tech Corp Ltd',
            'location' => 'Nairobi',
            'industry' => 'Technology',
            'profile' => 'Leading tech company in Kenya'
        ]);
        
        // Create high-match opportunity
        $highMatchOpp = Opportunity::create([
            'organization_id' => $org->id,
            'title' => 'Software Developer Intern',
            'description' => 'Looking for a software developer with Python and JavaScript skills',
            'type' => 'WBL',
            'status' => 'open',
            'location' => 'Nairobi'
        ]);
        
        $highMatchOpp->skills()->attach([$pythonSkill->id, $jsSkill->id, $webDevSkill->id]);
        
        // Create low-match opportunity
        $marketingSkill = Skill::where('name', 'Marketing')->first();
        $lowMatchOpp = Opportunity::create([
            'organization_id' => $org->id,
            'title' => 'Marketing Intern',
            'description' => 'Marketing and social media role',
            'type' => 'WBL',
            'status' => 'open',
            'location' => 'Nairobi'
        ]);
        
        $lowMatchOpp->skills()->attach([$marketingSkill->id]);
    }
}
```

```bash
# Run seeder
php artisan db:seed --class=MLDemoSeeder
```

---

## Part 5: Preparing for Defense Demo

### Demo Script (Follow This During Presentation)

**Setup Before Presentation:**
1. Open browser to login page
2. Have demo credentials ready
3. Clear browser cache for clean demo
4. Test internet connection

**Demo Flow (5-7 minutes):**

#### Scene 1: Student Login & ML Recommendations (2 min)

```
"Let me demonstrate as a Computer Science student named Demo Student..."

1. Login with demo.student@interlink.com / demo123
2. "Upon logging in, the system automatically analyzes my skills..."
3. Point to "Recommended for You" section
4. "As you can see, the ML algorithm identified Software Developer positions 
   as an 85% match because I have Python, JavaScript, and Web Development skills"
5. Hover over match score to show matched skills
6. "The system also shows Marketing positions with only 20% match - 
   this demonstrates the algorithm's ability to distinguish relevant from irrelevant opportunities"
```

#### Scene 2: Applying to Opportunities (1 min)

```
"Now let's apply to a high-match opportunity..."

1. Click on 85% match opportunity
2. "Before applying, the system confirms this is a great match"
3. Shows message: "Perfect match! Your skills align very well..."
4. Click Apply
5. "Application submitted successfully with match score recorded"
```

#### Scene 3: Organization View (2 min)

```
"From the organization's perspective..."

1. Logout and login as techcorp@interlink.com / demo123
2. Navigate to "My Opportunities"
3. Click on "Software Developer Intern"
4. "The system automatically ranks applicants by match score"
5. Point to Demo Student at top with 85% match
6. "Organizations can see exactly which skills match and which are missing"
7. "This saves time in screening and ensures best-fit candidates"
```

#### Scene 4: Admin Analytics (1 min)

```
"Finally, administrators have oversight..."

1. Login as admin (if time permits)
2. Show dashboard with placement statistics
3. "Admins can track ML matching performance and placement success rates"
```

---

## Part 6: Defense Presentation Metrics

### Metrics to Highlight

**From training_report.txt:**
```
ML Model Performance:
- Training Accuracy: ___%
- Average Match Score: ___%
- High Matches (≥60%): ___%
- Test Success Rate: ___%
```

**From test_results.json:**
```
Validation Results:
- Software Dev → Tech Role: ___% match
- Business Student → Marketing: ___% match
- Design Student → UI/UX: ___% match
```

**System Performance:**
```
- Total Opportunities: ___
- Students Registered: ___
- Successful Placements: ___
- Average Time to Placement: ___ days
```

---

## Part 7: Troubleshooting

### Issue: ML Model Not Loading

**Symptoms:** Dashboard shows no recommendations

**Solutions:**
```bash
# Check model file exists
ls -la storage/ml_models/tfidf_vectorizer.pkl

# Check permissions
chmod 644 storage/ml_models/*.pkl

# Check Laravel logs
tail -f storage/logs/laravel.log

# Verify Python works
python3 calculate_match.py "Python Web Development" "Python JavaScript" "storage/ml_models/tfidf_vectorizer.pkl"
```

### Issue: Match Scores All Zero

**Symptoms:** All opportunities show 0% match

**Solutions:**
```bash
# Retrain model
python3 train_ml_model.py

# Verify student has skills assigned
php artisan tinker
>>> $student = App\Models\Student::find(1);
>>> $student->skills;

# Check opportunity has skills
>>> $opp = App\Models\Opportunity::find(1);
>>> $opp->skills;
```

### Issue: Python Script Fails

**Symptoms:** shell_exec returns null

**Solutions:**
```bash
# Test Python directly
python3 -c "import pickle, sklearn; print('OK')"

# Check Python path
which python3

# Make script executable
chmod +x calculate_match.py

# Test script manually
python3 calculate_match.py "test" "test" "storage/ml_models/tfidf_vectorizer.pkl"
```

---

## Part 8: Final Checklist Before Defense

- [ ] ML model trained and tested (80%+ accuracy)
- [ ] Training report printed/saved
- [ ] Demo data seeded in database
- [ ] All features tested and working
- [ ] Screenshots taken of key features
- [ ] Demo credentials written down
- [ ] Backup video recording of demo
- [ ] Presentation slides prepared
- [ ] Project documentation printed
- [ ] System architecture diagram ready
- [ ] Anticipated questions prepared with answers

---

## Part 9: Quick Reference Commands

```bash
# Train model
python3 train_ml_model.py

# Test model
python3 test_ml_model.py

# Seed demo data
php artisan db:seed --class=MLDemoSeeder

# Clear caches
php artisan optimize:clear

# View logs
tail -f storage/logs/laravel.log

# Test ML matching
curl http://localhost/test-ml
```

---

## Part 10: Defense Day Protocol

### 1 Hour Before:
- [ ] Test internet connection
- [ ] Launch application and verify it works
- [ ] Test demo login credentials
- [ ] Verify projector connection
- [ ] Have backup (screenshots/video) ready

### During Presentation:
- [ ] Speak clearly and confidently
- [ ] Explain ML algorithm in simple terms
- [ ] Demonstrate real-time matching
- [ ] Show measurable results
- [ ] Answer questions directly

### Key Points to Emphasize:
1. **Innovation:** "ML matching vs manual search"
2. **Impact:** "Reduces placement time by X%"
3. **Accuracy:** "80%+ match accuracy in testing"
4. **Scalability:** "Can support multiple universities"

---

**Good Luck with Your Defense! 🎓**

Remember: You built a real, working system that solves a genuine problem. Be confident!
