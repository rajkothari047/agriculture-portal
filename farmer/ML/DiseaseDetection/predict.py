import sys, os, numpy as np, tensorflow as tf
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing import image

# ----------------------
# Disable TF logging
# ----------------------
os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"
tf.get_logger().setLevel("ERROR")

# ----------------------
# Default fallback values
# ----------------------
DEFAULT_OUTPUT = "Prediction failed,0,No output from AI model."

# ----------------------
# Check CLI args
# ----------------------
if len(sys.argv) < 3:
    print(DEFAULT_OUTPUT)
    sys.exit(0)

img_path = sys.argv[1]
crop_name = sys.argv[2].lower()  # grapevine or corn or tomato or potato

# ----------------------
# Set model path & class names
# ----------------------
BASE_DIR = os.path.dirname(__file__)
MODEL_DIR = os.path.join(BASE_DIR, "models")

if crop_name == "grapevine":
    MODEL_PATH = os.path.join(MODEL_DIR, "plant_disease_mobilenet.keras")
    CLASS_NAMES = [
        "Grapevine_Black_Rot",
        "Grapevine_ESCA",
        "Grapevine_Healthy",
        "Grapevine_Leaf_Blight"
    ]
    AI_EXPLANATIONS = {
        "Grapevine_Black_Rot": "Detected black lesions on leaves and shriveled fruit indicating Black Rot.",
        "Grapevine_ESCA": "Detected yellowing and necrosis consistent with ESCA.",
        "Grapevine_Healthy": "Leaf shows uniform green color, healthy plant.",
        "Grapevine_Leaf_Blight": "Detected brown/black lesions on leaves, indicating Leaf Blight."
    }

elif crop_name == "corn":
    MODEL_PATH = os.path.join(MODEL_DIR, "corn_disease_mobilenet.keras")

    # IMPORTANT: Keep same order as training labels
    CLASS_NAMES = [
        "Corn_Cercospora_leaf_spot Gray_leaf_spot",
        "Corn_Common_rust",
        "Corn_Northern_Leaf_Blight",
        "Corn_healthy"
    ]

    AI_EXPLANATIONS = {
        "Corn_healthy": "Leaf is green, firm, no spots or lesions detected by AI.",
        "Corn_Cercospora_leaf_spot Gray_leaf_spot": "Gray-brown spots on leaves indicate Gray Leaf Spot infection.",
        "Corn_Common_rust": "Orange-red pustules on leaves detected, showing Common Rust.",
        "Corn_Northern_Leaf_Blight": "Brown/necrotic lesions spreading across leaf, indicating Blight."
    }

elif crop_name == "tomato":
    MODEL_PATH = os.path.join(MODEL_DIR, "tomato_disease_mobilenet.keras")

    # IMPORTANT: Keep same order as training labels
    CLASS_NAMES = [
        "Tomato_Bacterial_spot",
        "Tomato_Early_blight",
        "Tomato_Late_blight",
        "Tomato_Leaf_Mold",
        "Tomato_Septoria_leaf_spot",
        "Tomato_Spider_mites Two-spotted_spider_mite",
        "Tomato_Target_Spot",
        "Tomato_healthy"
    ]

    AI_EXPLANATIONS = {
        "Tomato_Bacterial_spot": "Dark spots on leaves with yellow halos indicate Bacterial Spot infection.",
        "Tomato_Early_blight": "Large brown spots with concentric rings on leaves indicate Early Blight.",
        "Tomato_Late_blight": "Irregular brown lesions with white mold on leaf edges indicate Late Blight.",
        "Tomato_Leaf_Mold": "Yellowing of leaves with fuzzy mold underside indicates Leaf Mold.",
        "Tomato_Septoria_leaf_spot": "Small dark spots with gray centers indicate Septoria Leaf Spot.",
        "Tomato_Spider_mites Two-spotted_spider_mite": "Tiny yellow specks and webbing on leaves indicate Spider Mite infestation.",
        "Tomato_Target_Spot": "Dark circular spots with target-like rings indicate Target Spot.",
        "Tomato_healthy": "Leaf is green, firm, no spots or lesions detected by AI."
    }

elif crop_name == "potato":
    MODEL_PATH = os.path.join(MODEL_DIR, "potato_disease_mobilenet.keras")

    # IMPORTANT: Alphabetical order (same as folder order)
    CLASS_NAMES = [
        "Potato_Early_blight",
        "Potato_Late_blight",
        "Potato_healthy"
    ]

    AI_EXPLANATIONS = {
        "Potato_Early_blight": "Small brown spots with concentric rings indicate Early Blight infection.",
        "Potato_Late_blight": "Irregular dark lesions with white fungal growth indicates Late Blight.",
        "Potato_healthy": "Leaf is green, firm, no spots or lesions detected by AI."
    }

else:
    print(DEFAULT_OUTPUT)
    sys.exit(0)

# ----------------------
# Check if model exists
# ----------------------
if not os.path.exists(MODEL_PATH):
    print(DEFAULT_OUTPUT)
    sys.exit(0)

# ----------------------
# Load model
# ----------------------
try:
    model = load_model(MODEL_PATH, compile=False)
except:
    print(DEFAULT_OUTPUT)
    sys.exit(0)

# ----------------------
# Load and preprocess image
# ----------------------
if not os.path.exists(img_path):
    print(DEFAULT_OUTPUT)
    sys.exit(0)

try:
    # Important: convert to RGB (fixes Google image issues)
    img = image.load_img(img_path, target_size=(224, 224))
    img = img.convert("RGB")
    x = image.img_to_array(img)
    x = np.expand_dims(x, axis=0)
    x = x / 255.0
except:
    print(DEFAULT_OUTPUT)
    sys.exit(0)

# ----------------------
# Predict
# ----------------------
try:
    preds = model.predict(x, verbose=0)
    pred_index = int(np.argmax(preds[0]))
    predicted_class = CLASS_NAMES[pred_index]
    accuracy = float(preds[0][pred_index]) * 100
    accuracy = round(accuracy, 2)
except:
    print(DEFAULT_OUTPUT)
    sys.exit(0)

# ----------------------
# Confidence check
# ----------------------
if accuracy < 70:
    predicted_class = "Not sure (Please upload clear leaf image)"
    accuracy = round(accuracy, 2)
    ai_explanation = "Model is not confident. Please upload a clear image."
else:
    ai_explanation = AI_EXPLANATIONS.get(predicted_class, "No explanation available.")
    if accuracy > 90:
        ai_explanation += " The model is highly confident in this prediction."
    elif accuracy > 75:
        ai_explanation += " The model is fairly confident in this prediction."
    else:
        ai_explanation += " The model is less confident; consider re-uploading a clearer image."

# ----------------------
# Output CSV for PHP
# ----------------------
safe_explanation = ai_explanation.replace(",", ";")
print(f"{predicted_class},{accuracy:.2f},{safe_explanation}")
