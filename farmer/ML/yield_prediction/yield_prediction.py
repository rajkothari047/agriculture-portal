import pandas as pd
import numpy as np
import sys
import os
import joblib
import warnings
import time
warnings.filterwarnings('ignore')

# ------------------- File paths (using compressed models) -------------------
dir_path = os.path.dirname(__file__)
csv_file = os.path.join(dir_path, 'crop_production.csv')

# Use compressed models if they exist, otherwise use original
model_file = os.path.join(dir_path, 'yield_model_compressed.pkl')
encoder_file = os.path.join(dir_path, 'encoder_compressed.pkl')

# Fallback to original if compressed doesn't exist
if not os.path.exists(model_file):
    model_file = os.path.join(dir_path, 'yield_model.pkl')
    encoder_file = os.path.join(dir_path, 'encoder.pkl')

def load_data():
    """Load the All-India dataset"""
    print("📂 Loading crop_production.csv...", file=sys.stderr)
    
    if not os.path.exists(csv_file):
        raise FileNotFoundError(f"crop_production.csv not found in {dir_path}")
    
    df = pd.read_csv(csv_file)
    print(f"✓ Loaded {len(df):,} rows with {df.shape[1]} columns", file=sys.stderr)
    return df

def train_and_save_model():
    """Train the model and save it with compression"""
    print("\n" + "="*60, file=sys.stderr)
    print("🚀 TRAINING YIELD PREDICTION MODEL", file=sys.stderr)
    print("="*60, file=sys.stderr)
    start_time = time.time()
    
    # Load data
    df = load_data()
    
    # Use sample for faster training (50k rows is enough for good accuracy)
    SAMPLE_SIZE = 50000
    if SAMPLE_SIZE and len(df) > SAMPLE_SIZE:
        original_size = len(df)
        print(f"\n⚡ Using {SAMPLE_SIZE:,} row sample for faster training", file=sys.stderr)
        df = df.sample(n=SAMPLE_SIZE, random_state=42)
    
    # Drop Crop_Year if exists
    if 'Crop_Year' in df.columns:
        df = df.drop(['Crop_Year'], axis=1)
    
    # Handle missing values
    print("\n📊 Cleaning data...", file=sys.stderr)
    df = df.dropna(subset=['Production'])
    categorical_cols = ['State_Name', 'District_Name', 'Season', 'Crop']
    
    for col in categorical_cols:
        if col in df.columns:
            df[col] = df[col].fillna('Unknown')
    
    if 'Area' in df.columns:
        df['Area'] = df['Area'].fillna(df['Area'].median())
    
    # Separate features and target
    X = df.drop(['Production'], axis=1)
    y = df['Production']
    X = X.fillna('Unknown')
    y = y.fillna(y.median())
    
    from sklearn.model_selection import train_test_split
    from sklearn.preprocessing import OneHotEncoder
    from sklearn.ensemble import RandomForestRegressor
    
    # Split data
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
    
    # Train OneHotEncoder
    ohe = OneHotEncoder(handle_unknown='ignore', sparse_output=False)
    ohe.fit(X_train[categorical_cols])
    
    # Transform data
    X_train_categorical = ohe.transform(X_train[categorical_cols])
    X_train_final = np.hstack((X_train_categorical, X_train.drop(categorical_cols, axis=1).values))
    
    # Train model with fewer trees for faster loading
    model = RandomForestRegressor(n_estimators=50, random_state=42, n_jobs=-1)
    model.fit(X_train_final, y_train)
    
    # Save with compression (THIS IS THE KEY!)
    joblib.dump(model, model_file, compress=3)
    joblib.dump(ohe, encoder_file, compress=3)
    
    training_time = time.time() - start_time
    print(f"\n✅ Model trained and saved in {training_time:.1f} seconds", file=sys.stderr)
    
    # Show file sizes
    model_size = os.path.getsize(model_file) / (1024 * 1024)
    encoder_size = os.path.getsize(encoder_file) / (1024 * 1024)
    print(f"📁 Compressed model size: {model_size:.1f} MB", file=sys.stderr)
    print(f"📁 Compressed encoder size: {encoder_size:.1f} MB", file=sys.stderr)
    
    return model, ohe, categorical_cols

def load_model():
    """Load pre-trained compressed model from disk (FAST!)"""
    if os.path.exists(model_file) and os.path.exists(encoder_file):
        print("✓ Loading compressed pre-trained model...", file=sys.stderr)
        start_time = time.time()
        model = joblib.load(model_file)
        ohe = joblib.load(encoder_file)
        categorical_cols = ['State_Name', 'District_Name', 'Season', 'Crop']
        load_time = time.time() - start_time
        print(f"✓ Model loaded in {load_time:.2f} seconds", file=sys.stderr)
        
        # Show loaded model size
        model_size = os.path.getsize(model_file) / (1024 * 1024)
        print(f"📁 Loaded model size: {model_size:.1f} MB", file=sys.stderr)
        
        return model, ohe, categorical_cols
    else:
        print("⚠️ No cached model found. Training new model...", file=sys.stderr)
        return train_and_save_model()

# ------------------- Main execution -------------------
try:
    # Load or train model
    model, ohe, categorical_cols = load_model()
    
    # Get user input
    if len(sys.argv) >= 6:
        Jstate = sys.argv[1].strip('"').strip("'")
        Jdistrict = sys.argv[2].strip('"').strip("'")
        Jseason = sys.argv[3].strip('"').strip("'")
        Jcrops = sys.argv[4].strip('"').strip("'")
        Jarea = sys.argv[5].strip('"').strip("'")
    else:
        Jstate = 'Karnataka'
        Jdistrict = 'BAGALKOT'
        Jseason = 'Kharif'
        Jcrops = 'Rice'
        Jarea = '197'
    
    # Prepare input
    try:
        user_input = np.array([[Jstate, Jdistrict, Jseason, Jcrops, float(Jarea)]])
    except ValueError:
        user_input = np.array([[Jstate, Jdistrict, Jseason, Jcrops, 1.0]])
    
    # Transform and predict
    predict_start = time.time()
    user_input_categorical = ohe.transform(user_input[:, :4])
    user_input_final = np.hstack((user_input_categorical, user_input[:, 4:].astype(float).reshape(-1, 1)))
    prediction = model.predict(user_input_final)
    predicted_yield = max(0, float(prediction[0]))
    predict_time = time.time() - predict_start
    
    print(f"✓ Prediction completed in {predict_time:.3f} seconds", file=sys.stderr)
    print(f"{predicted_yield:.2f}")
    
except Exception as e:
    print(f"⚠️ Error: {e}", file=sys.stderr)
    print("0.00")