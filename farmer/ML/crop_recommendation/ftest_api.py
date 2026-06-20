#!/usr/bin/env python3
"""
Test the crop recommendation API
"""

import requests
import json

# API endpoint
url = 'http://127.0.0.1:5000/predict'

# Test data
test_data = {
    'n': 90,
    'p': 42,
    'k': 43,
    'temperature': 21,
    'humidity': 82,
    'ph': 6.5,
    'rainfall': 203
}

print("=" * 50)
print("Testing Crop Recommendation API")
print("=" * 50)
print(f"\nInput Parameters:")
print(json.dumps(test_data, indent=2))

try:
    response = requests.post(url, json=test_data, timeout=10)
    if response.status_code == 200:
        result = response.json()
        print(f"\n✓ Success!")
        print(f"Recommended Crop: {result['crop']}")
        print(f"Timestamp: {result['timestamp']}")
    else:
        print(f"\n✗ Error: HTTP {response.status_code}")
        print(response.text)
        
except requests.exceptions.ConnectionError:
    print("\n✗ Error: Cannot connect to API server")
    print("Please make sure the API server is running:")
    print("  Double-click fstart_api.bat")
except Exception as e:
    print(f"\n✗ Error: {str(e)}")