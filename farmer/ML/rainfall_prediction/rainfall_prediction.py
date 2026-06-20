import pandas as pd
import sys
import os

# Load the CSV from the same folder as the script
file_path = os.path.join(os.path.dirname(__file__), 'rainfall_in_india_1901-2015.csv')
df = pd.read_csv(file_path)

# Function to predict rainfall
def predict_rainfall(state, month):
    state_data = df[df['SUBDIVISION'] == state]
    avg_rainfall = state_data[month].mean()
    return avg_rainfall

# If-else for default values
if len(sys.argv) >= 3:
    Jregion = sys.argv[1]
    Jmonth = sys.argv[2]
else:
    Jregion = 'ANDAMAN & NICOBAR ISLANDS'
    Jmonth = 'JAN'

# Prediction
predicted_rainfall = predict_rainfall(Jregion, Jmonth)
print(predicted_rainfall)
