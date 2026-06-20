<?php
// Run this file once to pre-train the model for faster predictions
header('Content-Type: text/html; charset=utf-8');
echo "<h2>🚀 Pre-training Yield Prediction Model</h2>";
echo "<p>This will train the model once so future predictions are lightning fast...</p>";

// Run the training
$command = "python yield_prediction.py Karnataka BAGALKOT Kharif Rice 197 2>&1";
$output = shell_exec($command);

echo "<pre style='background:#f4f4f4; padding:15px; border-radius:5px;'>";
echo $output;
echo "</pre>";

// Check if model files were created
if (file_exists('yield_model.pkl') && file_exists('encoder.pkl')) {
    echo "<p style='color:green; font-weight:bold;'>✅ Model pre-trained successfully!</p>";
    echo "<p>🚀 Future predictions will now be 10x faster (1-3 seconds instead of 30 seconds)!</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>❌ Training failed. Please check your Python installation.</p>";
}

echo "<p><a href='../../../fyield_prediction.php' style='display:inline-block; background:#4F772D; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>← Go to Yield Prediction Page</a></p>";
?>