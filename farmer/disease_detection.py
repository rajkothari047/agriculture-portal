# disease_detection.py
import os
from flask import Flask, request, jsonify
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.image import img_to_array, load_img
import numpy as np

# --- Flask app ---
app = Flask(__name__)

# --- Load your trained models ---
BASE_MODEL_PATH = "ML/DiseaseDetection/models/"

MODELS = {
    "grapevine": {
        "path": BASE_MODEL_PATH + "plant_disease_mobilenet.keras",
        "classes": ["Grapevine_Black_Rot", "Grapevine_ESCA", "Grapevine_Healthy", "Grapevine_Leaf_Blight"]
    },
    "corn": {
        "path": BASE_MODEL_PATH + "corn_disease_mobilenet.keras",
        "classes": [
            "Corn_Cercospora_leaf_spot Gray_leaf_spot",
            "Corn_Common_rust",
            "Corn_Northern_Leaf_Blight",
            "Corn_healthy"
        ]
    },
    "tomato": {
        "path": BASE_MODEL_PATH + "tomato_disease_mobilenet.keras",
        "classes": [
            "Tomato_Bacterial_spot",
            "Tomato_Early_blight",
            "Tomato_Late_blight",
            "Tomato_Leaf_Mold",
            "Tomato_Septoria_leaf_spot",
            "Tomato_Spider_mites Two-spotted_spider_mite",
            "Tomato_Target_Spot",
            "Tomato_healthy"
        ]
    },
    "potato": {   # ✅ ADDED POTATO
        "path": BASE_MODEL_PATH + "potato_disease_mobilenet.keras",
        "classes": [
            "Potato_Early_blight",
            "Potato_healthy",
            "Potato_Late_blight"
        ]
    }
}

# Load models
loaded_models = {}
for crop, data in MODELS.items():
    loaded_models[crop] = load_model(data["path"], compile=False)

# --- Prediction function ---
def predict_disease(image_path, crop):
    model = loaded_models[crop]
    classes = MODELS[crop]["classes"]

    img = load_img(image_path, target_size=(224, 224))
    img_array = img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0)
    img_array = img_array / 255.0

    preds = model.predict(img_array)[0]
    class_idx = np.argmax(preds)
    confidence = float(preds[class_idx])

    return classes[class_idx], confidence

# --- Route to handle prediction ---
@app.route("/predict", methods=["POST"])
def predict():
    if "image" not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    file = request.files["image"]
    crop_name = request.form.get("crop_name", None)

    if not file or crop_name is None:
        return jsonify({"error": "Missing data"}), 400

    crop_name = crop_name.lower()

    if crop_name not in loaded_models:
        return jsonify({"error": "Invalid crop name"}), 400

    # Save uploaded file temporarily
    upload_path = os.path.join("uploads", file.filename)
    os.makedirs("uploads", exist_ok=True)
    file.save(upload_path)

    try:
        predicted_class, confidence = predict_disease(upload_path, crop_name)
        # Clean up uploaded file
        os.remove(upload_path)
        return jsonify({
            "predicted_class": predicted_class,
            "confidence": round(confidence, 4)
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 500

# --- Run Flask app ---
if __name__ == "__main__":
    app.run(debug=True)
