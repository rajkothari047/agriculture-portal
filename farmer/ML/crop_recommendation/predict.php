<?php
/**
 * PHP Bridge to Python Crop Recommendation
 * This file connects your PHP form to the Python model
 */

function predictCrop($n, $p, $k, $temp, $humidity, $ph, $rainfall) {
    // Convert to float and validate
    $n = floatval($n);
    $p = floatval($p);
    $k = floatval($k);
    $temp = floatval($temp);
    $humidity = floatval($humidity);
    $ph = floatval($ph);
    $rainfall = floatval($rainfall);
    
    // Path to Python script (same directory)
    $python_script = __DIR__ . '/frecommend.py';
    
    // Build command
    $command = sprintf(
        'python "%s" %s %s %s %s %s %s %s 2>&1',
        $python_script,
        $n, $p, $k, $temp, $humidity, $ph, $rainfall
    );
    
    // Execute Python script
    $output = shell_exec($command);
    $output = trim($output);
    
    // Check for errors
    if (empty($output)) {
        return "No response from prediction engine";
    }
    
    if (preg_match('/error|traceback|exception/i', $output)) {
        error_log("Crop Prediction Error: " . $output);
        return "Prediction temporarily unavailable";
    }
    
    return $output;
}
?>