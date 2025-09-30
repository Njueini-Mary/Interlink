# ML Data Generation & Virtual Environment Guide

This guide explains how I set up a Python virtual environment, install dependencies, and run my synthetic data generation script for ML matching in my Interlink project.

---

## 1. Create and Activate a Python Virtual Environment

```bash
# Create a virtual environment in the project root
python3 -m venv .venv

# Activate the virtual environment (Linux/macOS)
source .venv/bin/activate

# (On Windows, use: .venv\Scripts\activate)
```

---

## 2. Upgrade pip (recommended)
```bash
pip install --upgrade pip
```

---

## 3. Install Required Python Packages
```bash
pip install faker pandas
```

---

## 4. Run the Data Generation Script
```bash
# Make sure you are in the virtual environment
python3 ml_data_generator.py
```

This will generate the following files in your project directory:
- `students.csv`
- `opportunities.csv`
- `organizations.csv`
- `skills.csv`

---

## 5. Next Steps: Use the Data in Google Colab
1. Open [Google Colab](https://colab.research.google.com/).
2. Upload the generated CSV files.
3. Load them in Colab:
```python
import pandas as pd
students = pd.read_csv('students.csv')
opportunities = pd.read_csv('opportunities.csv')
organizations = pd.read_csv('organizations.csv')
skills = pd.read_csv('skills.csv')
```
4. Proceed with feature engineering, model training, and matching logic as described in my ML guide.

---

## 6. Deactivate the Virtual Environment (when done)
```bash
deactivate
```

---

## Troubleshooting
- If you get `python: command not found`, use `python3` instead of `python`.
- Always activate your virtual environment before running scripts or installing packages.
- If you add more dependencies, install them with `pip install ...` while the environment is active.

---

This process keeps my Python dependencies isolated and my ML workflow reproducible.
