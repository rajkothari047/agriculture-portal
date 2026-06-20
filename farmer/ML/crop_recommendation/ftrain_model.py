#!/usr/bin/env python3
"""
Run this file ONCE to train your model
After training, you can delete this file if you want
"""

import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
import joblib
import warnings
warnings.filterwarnings('ignore')

print("=" * 60)
print("Training Crop Recommendation Model")
print("=" * 60)

# Load your CSV file
df = pd.read_csv('Crop_recommendation.csv')
print(f"\n✓ Loaded {len(df)} records")

# Prepare data
X = df[['N', 'P', 'K', 'temperature', 'humidity', 'ph', 'rainfall']]
y = df['label']

# Encode labels
encoder = LabelEncoder()
y_encoded = encoder.fit_transform(y)

# Split and train
X_train, X_test, y_train, y_test = train_test_split(X, y_encoded, test_size=0.2, random_state=42)

model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# Evaluate
y_pred = model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)
print(f"✓ Model Accuracy: {accuracy * 100:.2f}%")

# Save model files
joblib.dump(model, 'crop_model.pkl')
joblib.dump(encoder, 'label_encoder.pkl')
print("✓ crop_model.pkl saved")
print("✓ label_encoder.pkl saved")

print("\n" + "=" * 60)
print("✅ TRAINING COMPLETE! Your model is ready to use.")
print("=" * 60)

# Test
sample = [[90, 42, 43, 21, 82, 6.5, 203]]
prediction = model.predict(sample)
crop = encoder.inverse_transform(prediction)[0]
print(f"\nTest: N=90, P=42, K=43, Temp=21°C, Humidity=82%, pH=6.5, Rainfall=203mm")
print(f"Result: {crop.upper()}")