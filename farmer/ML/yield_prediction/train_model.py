# train_model.py - Train with full dataset and show progress
import subprocess
import sys
import os
import time

print("=" * 70)
print("🚀 PRE-TRAINING YIELD PREDICTION MODEL (FULL DATASET)")
print("=" * 70)
print()
print("📊 Dataset: crop_production.csv (ALL 240,000 entries)")
print("⏱️  Expected time: 2-4 minutes (please wait)")
print()
print("🔄 Training in progress...")
print("   (This may take a few minutes - don't interrupt)")
print("-" * 70)
print()

start_time = time.time()

# Run the prediction script and show output in real-time
process = subprocess.Popen(
    [sys.executable, "-u", "yield_prediction.py", "Karnataka", "BAGALKOT", "Kharif", "Rice", "197"],
    stdout=subprocess.PIPE,
    stderr=subprocess.STDOUT,
    text=True,
    bufsize=1
)

# Print output in real-time
for line in process.stdout:
    print(line, end='')
    sys.stdout.flush()

process.wait()
training_time = time.time() - start_time

print()
print("=" * 70)
if process.returncode == 0:
    print("✅ TRAINING COMPLETE!")
    print("=" * 70)
    print(f"⏱️  Total time: {training_time:.1f} seconds ({training_time/60:.1f} minutes)")
    
    # Check model files
    if os.path.exists('yield_model.pkl'):
        size_mb = os.path.getsize('yield_model.pkl') / (1024 * 1024)
        print(f"📁 Model saved: yield_model.pkl ({size_mb:.1f} MB)")
    if os.path.exists('encoder.pkl'):
        size_mb = os.path.getsize('encoder.pkl') / (1024 * 1024)
        print(f"📁 Encoder saved: encoder.pkl ({size_mb:.1f} MB)")
    
    print()
    print("🎉" * 20)
    print("🚀 SUCCESS! Future predictions will be LIGHTNING FAST!")
    print("   (1-3 seconds instead of 2-4 minutes!)")
    print("🎉" * 20)
else:
    print("❌ TRAINING FAILED!")
    print(f"Error code: {process.returncode}")
    
print()