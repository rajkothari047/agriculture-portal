#!/usr/bin/env python3
"""
Fertilizer Recommendation - Called by PHP
"""

import sys
import joblib
import numpy as np
import os
import warnings
warnings.filterwarnings('ignore')

def predict_fertilizer(n, p, k, temperature, humidity, soil_moisture, soil_type, crop_type):
    """Predict fertilizer based on soil parameters and crop"""
    try:
        # Get the directory where this script is located
        script_dir = os.path.dirname(os.path.abspath(__file__))
        
        # Build full paths to model files
        model_path = os.path.join(script_dir, 'fertilizer_model.pkl')
        soil_encoder_path = os.path.join(script_dir, 'soil_encoder.pkl')
        crop_encoder_path = os.path.join(script_dir, 'crop_encoder.pkl')
        fertilizer_encoder_path = os.path.join(script_dir, 'fertilizer_encoder.pkl')
        
        # Check if model files exist
        if not os.path.exists(model_path):
            return f"ERROR: Model file not found"
        
        # Load model files
        model = joblib.load(model_path)
        le_soil = joblib.load(soil_encoder_path)
        le_crop = joblib.load(crop_encoder_path)
        le_fertilizer = joblib.load(fertilizer_encoder_path)
        
        # Encode categorical variables
        try:
            soil_encoded = le_soil.transform([soil_type])[0]
        except:
            # If soil type not found, use default
            soil_encoded = 0
            
        try:
            crop_encoded = le_crop.transform([crop_type])[0]
        except:
            # If crop type not found, use default
            crop_encoded = 0
        
        # Create feature array
        features = np.array([[float(n), float(p), float(k), 
                              float(temperature), float(humidity), 
                              float(soil_moisture), soil_encoded, crop_encoded]])
        
        # Predict
        prediction = model.predict(features)
        fertilizer_name = le_fertilizer.inverse_transform(prediction)[0]
        
        return fertilizer_name
    except Exception as e:
        return f"ERROR: {str(e)}"

if __name__ == "__main__":
    if len(sys.argv) == 9:
        result = predict_fertilizer(
            sys.argv[1], sys.argv[2], sys.argv[3], 
            sys.argv[4], sys.argv[5], sys.argv[6],
            sys.argv[7], sys.argv[8]
        )
        print(result)
    else:
        print("Usage: python frecommend_fertilizer.py N P K temperature humidity soil_moisture soil_type crop_type")