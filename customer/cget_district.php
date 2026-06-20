<?php
// cget_district.php - Get districts based on state
require('../sql.php');

if(isset($_POST['state_id'])) {
    $state_id = mysqli_real_escape_string($conn, $_POST['state_id']);
    
    $query = "SELECT * FROM district WHERE StCode = '$state_id' ORDER BY DistrictName";
    $result = mysqli_query($conn, $query);
    
    $options = '<option value="">Select District</option>';
    while($row = mysqli_fetch_assoc($result)) {
        $options .= '<option value="' . htmlspecialchars($row['DistrictName']) . '">' . htmlspecialchars($row['DistrictName']) . '</option>';
    }
    
    echo $options;
}
?>