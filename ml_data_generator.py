# ml_data_generator.py
# Script to generate synthetic data for Interlink ML matching
# Run: python ml_data_generator.py

from faker import Faker
import random
import pandas as pd

fake = Faker()

# Define skills and courses
skills = ['Python', 'Data Science', 'Web Dev', 'AI', 'Marketing', 'Finance', 'Design', 'Project Management']
courses = ['Computer Science', 'Business', 'Engineering', 'Design', 'Finance', 'Marketing']
organizations = [fake.company() for _ in range(20)]

# Generate Students
def generate_students(n=500):
    students = []
    for _ in range(n):
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
    return students

# Generate Opportunities
def generate_opportunities(n=200):
    opportunities = []
    for _ in range(n):
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
    return opportunities

# Generate Organizations
def generate_organizations():
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
    return orgs

# Generate Skills
def generate_skills():
    return [{'name': skill} for skill in skills]

if __name__ == "__main__":
    students = generate_students()
    opportunities = generate_opportunities()
    orgs = generate_organizations()
    skills_data = generate_skills()

    pd.DataFrame(students).to_csv('students.csv', index=False)
    pd.DataFrame(opportunities).to_csv('opportunities.csv', index=False)
    pd.DataFrame(orgs).to_csv('organizations.csv', index=False)
    pd.DataFrame(skills_data).to_csv('skills.csv', index=False)

    print("Synthetic data generated: students.csv, opportunities.csv, organizations.csv, skills.csv")
