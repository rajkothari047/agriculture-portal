#!/usr/bin/env python3
"""
Crop Recommendation API Server using CSV-trained model
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import numpy as np
import pandas as pd
import os
import sys
import logging
from datetime import datetime
from pathlib import Path

# Setup logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('crop_api.log'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)

# Global variables
model = None
label_encoder = None

def load_model():
    """Load the trained model and encoder"""
    global model, label_encoder
    
    script_dir = Path(__file__).parent
    model_path = script_dir / 'crop_model.pkl'
    encoder_path = script_dir / 'label_encoder.pkl'
    
    if not model_path.exists() or not encoder_path.exists():
        logger.error("Model files not found! Please run ftrain_model.py first")
        return False
    
    try:
        model = joblib.load(model_path)
        label_encoder = joblib.load(encoder_path)
        logger.info("Model loaded successfully")
        logger.info(f"Available crops: {list(label_encoder.classes_)}")
        return True
    except Exception as e:
        logger.error(f"Error loading model: {str(e)}")
        return False

@app.route('/predict', methods=['POST'])
def predict():
    """Predict crop based on input parameters"""
    try:
        data = request.json
        
        # Extract values (support both naming conventions)
        n = float(data.get('n', data.get('N', 0)))
        p = float(data.get('p', data.get('P', 0)))
        k = float(data.get('k', data.get('K', 0)))
        temperature = float(data.get('temperature', data.get('t', data.get('temp', 0))))
        humidity = float(data.get('humidity', data.get('h', 0)))
        ph = float(data.get('ph', data.get('pH', 0)))
        rainfall = float(data.get('rainfall', data.get('r', 0)))
        
        # Create feature array
        features = np.array([[n, p, k, temperature, humidity, ph, rainfall]])
        
        # Make prediction
        prediction = model.predict(features)
        crop_name = label_encoder.inverse_transform(prediction)[0]
        
        logger.info(f"Prediction: {crop_name} | N={n}, P={p}, K={k}, Temp={temperature}°C, Hum={humidity}%, pH={ph}, Rain={rainfall}mm")
        
        return jsonify({
            'success': True,
            'crop': crop_name,
            'message': 'Prediction successful',
            'timestamp': datetime.now().isoformat()
        })
        
    except ValueError as e:
        logger.error(f"Value error: {str(e)}")
        return jsonify({
            'success': False,
            'error': 'Invalid parameter values. Please ensure all values are numbers.'
        }), 400
    except Exception as e:
        logger.error(f"Prediction error: {str(e)}")
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'running',
        'model_loaded': model is not None,
        'timestamp': datetime.now().isoformat()
    })

@app.route('/info', methods=['GET'])
def model_info():
    """Get model information"""
    if model and label_encoder:
        return jsonify({
            'model_type': str(type(model).__name__),
            'features': ['N', 'P', 'K', 'temperature', 'humidity', 'ph', 'rainfall'],
            'available_crops': label_encoder.classes_.tolist(),
            'status': 'ready'
        })
    else:
        return jsonify({
            'status': 'not_ready',
            'message': 'Model not loaded'
        }), 503

if __name__ == '__main__':
    # Load model before starting
    print("=" * 50)
    print("Crop Recommendation API Server")
    print("=" * 50)
    
    if load_model():
        port = int(os.environ.get('API_PORT', 5000))
        print(f"\n✓ Server starting on http://127.0.0.1:{port}")
        print("✓ API endpoints:")
        print("  - POST /predict - Get crop recommendation")
        print("  - GET  /health - Health check")
        print("  - GET  /info   - Model information")
        print("\nPress Ctrl+C to stop the server\n")
        app.run(host='127.0.0.1', port=port, debug=False, threaded=True)
    else:
        print("\n✗ ERROR: Failed to load model")
        print("Please run: python ftrain_model.py")
        sys.exit(1)