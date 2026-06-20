<?php
// process_prediction.php - Handles Python execution and returns HTML for AJAX

// Include necessary session/database files (keep your original include)
include ('fsession.php'); 

// Define color variables again for styling in this file
// Keeping colors separate to prevent scope issues in the AJAX response
echo '<style>
    :root {
        --color-primary-dark: #0A3D0A; 
        --color-accent-terracotta: #B85C38; 
        --color-secondary-green: #4F772D; 
        --color-bg-light: #F9F7F3; 
        --color-text-dark: #1E293B; 
    }
    .badge-success-custom {
        background-color: var(--color-secondary-green) !important;
        color: white !important;
    }
    .debug-box {
        background: #fff3cd; 
        color: #856404; 
        border: 1px solid #ffeeba; 
        padding: 15px; 
        margin-bottom: 20px;
        border-radius: 5px;
        font-family: monospace;
        white-space: pre-wrap; /* Ensure long outputs wrap */
    }
</style>';


// Check if data was sent via POST from AJAX
if(isset($_POST['state'], $_POST['district'], $_POST['season'])){
    
    $state=trim($_POST['state']);
    $district=trim($_POST['district']);
    $season=trim($_POST['season']);

    // --- EXECUTION AND DEBUGGING ---
    $JsonState=json_encode($state);
    $JsonDistrict=json_encode($district);
    $JsonSeason=json_encode($season);
    
    // 1. Full command to be executed
    $command = escapeshellcmd("python ML/crop_prediction/ZDecision_Tree_Model_Call.py $JsonState $JsonDistrict $JsonSeason");
    
    // Execute Python script
    $raw_output = shell_exec($command);
    
    // Process the output: split by comma, clean, and filter empty entries
    $raw_output_clean = trim(str_replace(['"', '[', ']'], '', $raw_output));
    $crop_array = array_filter(array_map('trim', explode(',', $raw_output_clean)));

    
    // --- START DEBUG OUTPUT ---
    $html = '<div class="debug-box">';
    $html .= '<h4>⚠️ DEBUGGING INFORMATION</h4>';
    $html .= '<strong>1. Command Executed:</strong> ' . htmlspecialchars($command) . '<br><hr>';
    $html .= '<strong>2. Raw Output from Python Script:</strong> ' . htmlspecialchars($raw_output) . '<br><hr>';
    $html .= '<strong>3. Cleaned Output String:</strong> ' . htmlspecialchars($raw_output_clean) . '<br><hr>';
    $html .= '<strong>4. PHP Parsed Array ($crop_array):</strong> ';
    $html .= print_r($crop_array, true);
    $html .= '</div>';
    // --- END DEBUG OUTPUT ---


    // --- NORMAL HTML OUTPUT (The styling part you want) ---

    // 1. User Input Summary
    $html .= '<div class="alert py-4" role="alert" style="background-color: var(--color-bg-light); border-left: 5px solid var(--color-accent-terracotta);"> ';
    $html .= '<h4 class="alert-heading font-weight-bold" style="color: var(--color-text-dark);">Parameters Submitted:</h4>';
    $html .= '<p class="mb-0" style="color: var(--color-text-dark);"><strong>State:</strong> '.$state.' | <strong>District:</strong> '.$district.' | <strong>Season:</strong> '.$season.'</p>';
    $html .= '</div>';
    
    $html .= '<hr class="my-4">';
    $html .= '<h3 class="mb-4 text-center" style="color: var(--color-secondary-green);">Crops Recommended for Maximum Yield:</h3>';

    // 2. Prediction Table
    if (!empty($crop_array)) {
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table align-items-center">';
        $html .= '<tbody>';
        
        foreach ($crop_array as $crop) {
            if (empty($crop)) continue;
            $html .= '<tr>';
            $html .= '<th scope="row" class="px-2 py-3" style="width: 50px;">';
            $html .= '<i class="ni ni-ui-04" style="font-size: 1.4rem; color: var(--color-secondary-green) !important;"></i>';
            $html .= '</th>';

            $html .= '<td class="py-3">';
            $html .= '<span class="h5 font-weight-bold" style="color: var(--color-text-dark);">' . htmlspecialchars($crop) . '</span>';
            $html .= '</td>';
            
            $html .= '<td class="text-right py-3">';
            $html .= '<span class="badge badge-pill badge-success-custom">Recommended</span>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
    } else {
        $html .= '<div class="alert text-center" role="alert" style="background-color: #f0f0f0;">';
        $html .= '<p class="lead mb-0" style="color: var(--color-text-dark);"><i class="ni ni-notification-70 mr-2"></i> **Note:** The crop list is empty. Check debug information above.</p>';
        $html .= '</div>';
    }

    // Output the generated HTML
    echo $html;

} else {
    // Handle case where script is accessed directly without POST data
    echo '<div class="alert alert-danger text-center" role="alert">Error: Invalid request. No parameters received.</div>';
}
?>