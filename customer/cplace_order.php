<?php
include ('csession.php');
include ('../sql.php');

if(isset($_POST['place_order']) && !empty($_SESSION["shopping_cart"])) {
    $cust_user = $_SESSION['customer_login_user'];
    
    // Iterate through cart and save to your database (e.g., an orders table)
    foreach($_SESSION["shopping_cart"] as $keys => $values) {
        $crop = $values["item_name"];
        $quantity = $values["item_quantity"];
        $price = $values["item_price"];
        
        // Example Query: Adjust table name and columns to match your database
        $sql = "INSERT INTO orders (customer_name, crop_name, quantity, total_price, status) 
                VALUES ('$cust_user', '$crop', '$quantity', '$price', 'Pending')";
        mysqli_query($conn, $sql);

        // Also delete from the main cart table in database if you're tracking it there
        mysqli_query($conn, "DELETE FROM `cart` WHERE `cropname` = '$crop'");
    }

    // Clear the session cart
    unset($_SESSION["shopping_cart"]);
    unset($_SESSION["Total_Cart_Price"]);

    echo "<script>alert('Order Placed Successfully!'); window.location.href='cbuy_crops.php';</script>";
} else {
    header("location: cbuy_crops.php");
}
?>