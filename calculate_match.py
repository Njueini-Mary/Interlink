#!/usr/bin/env python3
import sys
import pickle
from sklearn.metrics.pairwise import cosine_similarity

import numpy as np
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier

try:
    student_text = sys.argv[1]
    opportunity_text = sys.argv[2]
    vectorizer_path = sys.argv[3]
    logreg_path = sys.argv[4]
    rf_path = sys.argv[5]

    # Load vectorizer
    with open(vectorizer_path, 'rb') as f:
        vectorizer = pickle.load(f)

    # Transform texts
    student_vec = vectorizer.transform([student_text]).toarray()[0]
    opp_vec = vectorizer.transform([opportunity_text]).toarray()[0]

    # TF-IDF Cosine Similarity
    student_vec_csr = vectorizer.transform([student_text])
    opp_vec_csr = vectorizer.transform([opportunity_text])
    similarity = cosine_similarity(student_vec_csr, opp_vec_csr)[0][0]
    tfidf_score = similarity * 100

    # Prepare input for classifiers
    X = np.concatenate([student_vec, opp_vec]).reshape(1, -1)

    # Logistic Regression
    with open(logreg_path, 'rb') as f:
        logreg = pickle.load(f)
    logreg_prob = logreg.predict_proba(X)[0][1] * 100

    # Random Forest
    with open(rf_path, 'rb') as f:
        rf = pickle.load(f)
    rf_prob = rf.predict_proba(X)[0][1] * 100

    # Output all scores as CSV
    print(f"{round(tfidf_score,2)},{round(logreg_prob,2)},{round(rf_prob,2)}")

except Exception as e:
    print("0,0,0")  # Return 0s on error