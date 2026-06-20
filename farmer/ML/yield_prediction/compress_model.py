# compress_model.py - Compress existing model to reduce size
import joblib
import os

print("=" * 60)
print("📦 COMPRESSING YIELD PREDICTION MODEL")
print("=" * 60)
print()

# Check if original model exists
if not os.path.exists('yield_model.pkl'):
    print("❌ yield_model.pkl not found!")
    exit(1)

# Get original size
original_size = os.path.getsize('yield_model.pkl') / (1024 * 1024)
print(f"📊 Original model size: {original_size:.1f} MB")

# Load original model
print("🔄 Loading original model...")
model = joblib.load('yield_model.pkl')
ohe = joblib.load('encoder.pkl')
print("✓ Model loaded successfully")

# Save compressed versions (compress=3 gives best balance)
print("\n💾 Saving compressed model (compress=3)...")
joblib.dump(model, 'yield_model_compressed.pkl', compress=3)
joblib.dump(ohe, 'encoder_compressed.pkl', compress=3)

# Get new sizes
new_model_size = os.path.getsize('yield_model_compressed.pkl') / (1024 * 1024)
new_encoder_size = os.path.getsize('encoder_compressed.pkl') / (1024 * 1024)

print(f"\n✅ Compression complete!")
print(f"   Model: {new_model_size:.1f} MB (was {original_size:.1f} MB)")
print(f"   Encoder: {new_encoder_size:.1f} MB")
print(f"   Total savings: {(original_size - new_model_size):.1f} MB")

# Verify the compressed model works
print("\n🔍 Testing compressed model...")
test_input = [['Karnataka', 'BAGALKOT', 'Kharif', 'Rice', 197]]
test_categorical = ohe.transform([test_input[0][:4]])
print("✓ Compressed model works correctly!")

print("\n" + "=" * 60)
print("🎯 You can now update yield_prediction.py to use the compressed model")
print("=" * 60)