import pandas as pd
import numpy as np
import sys
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier

# -------------------------
# Read dataset
# -------------------------
dataset = pd.read_csv('Crop_recommendation.csv')  # File must be in same folder

X = dataset.iloc[:, :-1].values
y = dataset.iloc[:, -1].values

# Split dataset
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=0)

# Train model
classifier = RandomForestClassifier(n_estimators=10, criterion='entropy', random_state=0)
classifier.fit(X_train, y_train)

# -------------------------
# Handle user input
# -------------------------
# Default example values (useful when running without CLI input)
default_values = [90, 40, 40, 20, 80, 6.5, 200]

# If 7 values are passed via command line → use them
if len(sys.argv) == 8:
    try:
        n_params = float(sys.argv[1])
        p_params = float(sys.argv[2])
        k_params = float(sys.argv[3])
        t_params = float(sys.argv[4])
        h_params = float(sys.argv[5])
        ph_params = float(sys.argv[6])
        r_params = float(sys.argv[7])
        user_values = [n_params, p_params, k_params, t_params, h_params, ph_params, r_params]
    except:
        print("Invalid inputs. Using default values instead.")
        user_values = default_values
else:
    # No CLI inputs → use default test values
    user_values = default_values

# Make input array
user_input = np.array([user_values])

# Predict
prediction = classifier.predict(user_input)

print("\nPredicted Crop:", prediction[0])
