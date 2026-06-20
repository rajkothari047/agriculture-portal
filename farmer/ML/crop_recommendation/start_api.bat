@echo off
TITLE Crop Recommendation API Server
color 0A

echo ========================================
echo   Crop Recommendation API Server
echo ========================================
echo.

cd /d "%~dp0"

echo [1/4] Checking Python installation...
python --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Python is not installed or not in PATH
    echo Please install Python 3.7 or higher from https://python.org
    pause
    exit /b 1
)
python --version
echo.

echo [2/4] Checking model files...
if not exist "crop_model.pkl" (
    echo [WARNING] Model not found. Training new model from CSV...
    echo This may take a few moments...
    python ftrain_model.py
    if errorlevel 1 (
        echo [ERROR] Model training failed
        echo Please check if Crop_recommendation.csv exists and has correct format
        pause
        exit /b 1
    )
    echo [OK] Model trained successfully
) else (
    echo [OK] Model files found
)
echo.

echo [3/4] Installing/updating dependencies...
pip install -r requirements.txt >nul 2>&1
if errorlevel 1 (
    echo [WARNING] Some dependencies may need manual installation
    echo Try running: pip install flask flask-cors scikit-learn pandas numpy joblib
) else (
    echo [OK] Dependencies ready
)
echo.

echo [4/4] Starting API Server...
echo.
echo ========================================
echo Server will run on: http://127.0.0.1:5000
echo Press Ctrl+C to stop the server
echo ========================================
echo.

python fapi.py

pause