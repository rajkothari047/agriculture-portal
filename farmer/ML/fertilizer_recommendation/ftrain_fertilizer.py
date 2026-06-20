#!/usr/bin/env python3
"""
Train Fertilizer Recommendation Model from CSV
Run this ONCE to train the model
"""

import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
import joblib
import warnings
import os
warnings.filterwarnings('ignore')

print("=" * 60)
print("Fertilizer Recommendation Model Training")
print("=" * 60)

# Load the CSV file
csv_path = 'fertilizer_recommendation.csv'
print(f"\n1. Loading data from: {csv_path}")

try:
    df = pd.read_csv(csv_path)
    print(f"   ✓ Loaded {len(df)} records")
    print(f"   ✓ Columns: {list(df.columns)}")
except Exception as e:
    print(f"   ✗ Error loading file: {e}")
    exit(1)

# Check column names and map them (UPDATED MAPPING)
print("\n2. Checking and mapping column names...")

# Define expected columns and their possible variations
column_mapping = {}
for col in df.columns:
    col_lower = col.lower().strip()
    if col_lower in ['n', 'nitrogen']:
        column_mapping[col] = 'N'
    elif col_lower in ['p', 'phosphorus', 'phosphorous']:
        column_mapping[col] = 'P'
    elif col_lower in ['k', 'potassium']:
        column_mapping[col] = 'K'
    elif col_lower in ['temperature', 'temp', 'temparature']:
        column_mapping[col] = 'temperature'
    elif col_lower in ['humidity', 'humid']:
        column_mapping[col] = 'humidity'
    elif col_lower in ['soil_moisture', 'moisture', 'soil moisture']:
        column_mapping[col] = 'soil_moisture'
    elif col_lower in ['soil_type', 'soil', 'soil type']:
        column_mapping[col] = 'soil_type'
    elif col_lower in ['crop_type', 'crop', 'crop type']:
        column_mapping[col] = 'crop_type'
    elif col_lower in ['fertilizer', 'fertilizer_name', 'fertilizer name']:
        column_mapping[col] = 'fertilizer'

# Rename columns
df = df.rename(columns=column_mapping)
print(f"   Column mapping applied:")
for old, new in column_mapping.items():
    print(f"   {old} → {new}")

# Verify required columns (check for original names as well)
required_columns = ['N', 'P', 'K', 'temperature', 'humidity', 'soil_moisture', 'soil_type', 'crop_type', 'fertilizer']
missing_columns = [col for col in required_columns if col not in df.columns]

if missing_columns:
    print(f"\n   ✗ WARNING: Missing columns: {missing_columns}")
    print(f"   Available columns: {list(df.columns)}")
    print(f"\n   Attempting to use available columns...")
    
    # Try to use original column names if mapping didn't work
    if 'Soil Type' in df.columns:
        df['soil_type'] = df['Soil Type']
    if 'Crop Type' in df.columns:
        df['crop_type'] = df['Crop Type']
    if 'Fertilizer Name' in df.columns:
        df['fertilizer'] = df['Fertilizer Name']
    
    # Re-check
    required_columns = ['N', 'P', 'K', 'temperature', 'humidity', 'soil_moisture', 'soil_type', 'crop_type', 'fertilizer']
    missing_columns = [col for col in required_columns if col not in df.columns]
    
    if missing_columns:
        print(f"   ✗ Still missing: {missing_columns}")
        print(f"   Please check your CSV column names.")
        exit(1)

print(f"\n   ✓ All required columns present")

# Prepare features and labels
print("\n3. Preparing features and labels...")

# Encode categorical variables
le_soil = LabelEncoder()
le_crop = LabelEncoder()
le_fertilizer = LabelEncoder()

df['soil_type_encoded'] = le_soil.fit_transform(df['soil_type'])
df['crop_type_encoded'] = le_crop.fit_transform(df['crop_type'])
df['fertilizer_encoded'] = le_fertilizer.fit_transform(df['fertilizer'])

# Features
feature_columns = ['N', 'P', 'K', 'temperature', 'humidity', 'soil_moisture', 'soil_type_encoded', 'crop_type_encoded']
X = df[feature_columns]
y = df['fertilizer_encoded']

print(f"   Features shape: {X.shape}")
print(f"   Number of unique fertilizers: {len(le_fertilizer.classes_)}")
print(f"   Fertilizers: {list(le_fertilizer.classes_)}")

# Split data for validation
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train model
print("\n4. Training Random Forest model...")
model = RandomForestClassifier(
    n_estimators=100,
    max_depth=20,
    random_state=42,
    n_jobs=-1
)
model.fit(X_train, y_train)

# Evaluate model
print("\n5. Evaluating model...")
y_pred = model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)
print(f"   ✓ Model Accuracy: {accuracy * 100:.2f}%")

# Save model and encoders
print("\n6. Saving model files...")
joblib.dump(model, 'fertilizer_model.pkl')
joblib.dump(le_soil, 'soil_encoder.pkl')
joblib.dump(le_crop, 'crop_encoder.pkl')
joblib.dump(le_fertilizer, 'fertilizer_encoder.pkl')
print("   ✓ fertilizer_model.pkl saved")
print("   ✓ soil_encoder.pkl saved")
print("   ✓ crop_encoder.pkl saved")
print("   ✓ fertilizer_encoder.pkl saved")

# Feature importance
print("\n7. Feature Importance:")
feature_importance = pd.DataFrame({
    'feature': ['Nitrogen (N)', 'Phosphorus (P)', 'Potassium (K)', 
                'Temperature', 'Humidity', 'Soil Moisture', 'Soil Type', 'Crop Type'],
    'importance': model.feature_importances_
}).sort_values('importance', ascending=False)

for idx, row in feature_importance.iterrows():
    bar = '█' * int(row['importance'] * 50)
    print(f"   {row['feature']:15s}: {row['importance']*100:5.1f}% {bar}")

print("\n" + "=" * 60)
print("✓ Fertilizer Model training completed successfully!")
print("=" * 60)

# Test prediction example
print("\n📊 Test Prediction Example:")
print("   Model ready for predictions!")
print("\n   You can now test with:")
print('   python frecommend_fertilizer.py 37 0 0 26 52 38 "Loamy" "Maize"')