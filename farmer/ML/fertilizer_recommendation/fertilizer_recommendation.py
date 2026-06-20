import pandas as pd
import sys
from sklearn.preprocessing import LabelEncoder
from sklearn.tree import DecisionTreeClassifier

# Load dataset
data = pd.read_csv("fertilizer_recommendation.csv")

# Label encoding
le_soil = LabelEncoder()
data['Soil Type'] = le_soil.fit_transform(data['Soil Type'])

le_crop = LabelEncoder()
data['Crop Type'] = le_crop.fit_transform(data['Crop Type'])

# Split features and target
X = data.iloc[:, :8]
y = data.iloc[:, -1]

# Train model
model = DecisionTreeClassifier(random_state=0)
model.fit(X, y)

# -------------------------------------------------------
# Default values (used when no CLI inputs are given)
# -------------------------------------------------------
default = {
    "N": 50,
    "P": 40,
    "K": 40,
    "Temperature": 25,
    "Humidity": 60,
    "Moisture": 40,
    "Soil Type": "Sandy",
    "Crop Type": "Maize"
}

# -------------------------------------------------------
# If EXACTLY 8 arguments are passed, use them
# -------------------------------------------------------
if len(sys.argv) == 9:
    N = float(sys.argv[1])
    P = float(sys.argv[2])
    K = float(sys.argv[3])
    T = float(sys.argv[4])
    H = float(sys.argv[5])
    M = float(sys.argv[6])
    soil = sys.argv[7]
    crop = sys.argv[8]
else:
    # No input → use defaults
    N = default["N"]
    P = default["P"]
    K = default["K"]
    T = default["Temperature"]
    H = default["Humidity"]
    M = default["Moisture"]
    soil = default["Soil Type"]
    crop = default["Crop Type"]

# Encode soil & crop
soil_enc = le_soil.transform([soil])[0]
crop_enc = le_crop.transform([crop])[0]

# Prepare input row
row = [[T, H, M, soil_enc, crop_enc, N, K, P]]
row = pd.DataFrame(row, columns=X.columns)

# Predict
result = model.predict(row)[0]

print(result)
