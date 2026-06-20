<?php
include ('csession.php');
include ('../sql.php');

if(isset($_POST['place_order']) && !empty($_SESSION["shopping_cart"])) {
    $customer = $_SESSION['customer_login_user'];
    
    // Optional: Loop through the cart and save to your 'orders' table if you have one
    foreach($_SESSION["shopping_cart"] as $keys => $values) {
        $item = $values["item_name"];
        $qty = $values["item_quantity"];
        $price = $values["item_price"];
        
        // Example: mysqli_query($conn, "INSERT INTO cust_orders (customer, crop, qty, price) VALUES ('$customer', '$item', '$qty', '$price')");
    }

    // Clear the cart session and database cart table
    unset($_SESSION["shopping_cart"]);
    mysqli_query($conn, "DELETE FROM `cart` WHERE `custname` = '$customer'"); 

    echo '<script>alert("Order Placed Successfully!"); window.location.href="cbuy_crops.php";</script>';
} else {
    header("location: cbuy_crops.php");
}
?>