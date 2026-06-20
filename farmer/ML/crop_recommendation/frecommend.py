#!/usr/bin/env python3
"""
Crop recommendation - called by PHP
"""

import sys
import joblib
import numpy as np
import os
import warnings
warnings.filterwarnings('ignore')

def predict_crop(n, p, k, temperature, humidity, ph, rainfall):
    """Predict crop based on soil parameters"""
    try:
        # Get the directory where this script is located
        script_dir = os.path.dirname(os.path.abspath(__file__))
        
        # Build full paths to model files
        model_path = os.path.join(script_dir, 'crop_model.pkl')
        encoder_path = os.path.join(script_dir, 'label_encoder.pkl')
        
        # Check if model files exist
        if not os.path.exists(model_path):
            return f"ERROR: Model file not found at {model_path}"
        if not os.path.exists(encoder_path):
            return f"ERROR: Encoder file not found at {encoder_path}"
        
        # Load model files
        model = joblib.load(model_path)
        encoder = joblib.load(encoder_path)
        
        # Create feature array
        features = np.array([[float(n), float(p), float(k), 
                              float(temperature), float(humidity), 
                              float(ph), float(rainfall)]])
        
        # Predict
        prediction = model.predict(features)
        crop_name = encoder.inverse_transform(prediction)[0]
        
        return crop_name
    except Exception as e:
        return f"ERROR: {str(e)}"

if __name__ == "__main__":
    if len(sys.argv) == 8:
        result = predict_crop(sys.argv[1], sys.argv[2], sys.argv[3], 
                              sys.argv[4], sys.argv[5], sys.argv[6], sys.argv[7])
        print(result)
    else:
        print("Usage: python frecommend.py N P K temperature humidity pH rainfall")