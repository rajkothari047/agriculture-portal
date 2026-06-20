<?php
session_start();
include('../sql.php'); // your DB connection

if(!isset($_SESSION['customer_login_user'])){
    echo "Login required";
    exit;
}

$user = $_SESSION['customer_login_user'];

// Get form data
$fname = $_POST['first_name'] ?? '';
$lname = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$district = $_POST['district'] ?? '';
$state = $_POST['state'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'Card';

$order_group_id = uniqid("ORD_");

// DEBUG (optional)
// print_r($_POST);

if(!empty($_SESSION["shopping_cart"])){

    foreach($_SESSION["shopping_cart"] as $item){

        $crop = $item["item_name"];
        $qty  = $item["item_quantity"];
        $price= $item["item_price"];

        $sql = "INSERT INTO orders 
        (customer_user, crop_name, quantity, total_price, order_date, status,
         order_group_id, first_name, last_name, email, phone, address, city, district, state, pincode,
         payment_method, payment_status)

        VALUES
        ('$user','$crop','$qty','$price',NOW(),'Pending',
         '$order_group_id','$fname','$lname','$email','$phone','$address','$city','$district','$state','$pincode',
         '$payment_method','Success')";

        if(!mysqli_query($conn, $sql)){
            echo "ERROR: " . mysqli_error($conn);
            exit;
        }
    }

    // clear cart after order
    unset($_SESSION["shopping_cart"]);

    echo "SUCCESS";
} else {
    echo "Cart Empty";
}
?>