<?php
session_start();
require('../sql.php'); 

if(isset($_POST['add_to_cart'])){
    $crop = mysqli_real_escape_string($conn, $_POST['crops']);
    $quantity = (int)$_POST['quantity'];
    $tradeID = $_POST['tradeid'];
    $price = (float)$_POST['price'];

    // 1. UPDATE DATABASE 
    // Uses ON DUPLICATE KEY UPDATE to prevent the "Duplicate Entry" Fatal Error
    $query4 = "INSERT INTO `cart`(`cropname`, `quantity`, `price`) 
               VALUES ('$crop', '$quantity', '$price')
               ON DUPLICATE KEY UPDATE 
               quantity = quantity + VALUES(quantity),
               price = price + VALUES(price)";
    
    mysqli_query($conn, $query4);

    // 2. UPDATE SESSION
    if(!isset($_SESSION["shopping_cart"])) {
        $_SESSION["shopping_cart"] = array();
    }

    $item_ids = array_column($_SESSION["shopping_cart"], "item_id");

    if(!in_array($tradeID, $item_ids)) {
        // New item
        $item_array = array(
            'item_id'       => $tradeID,
            'item_name'     => $_POST["crops"],
            'item_price'    => $price,
            'item_quantity' => $quantity
        );
        array_push($_SESSION['shopping_cart'], $item_array);
    } else {
        // Existing item in session: Update quantity and price
        foreach($_SESSION["shopping_cart"] as &$item) {
            if($item['item_id'] == $tradeID) {
                $item['item_quantity'] += $quantity;
                $item['item_price'] += $price;
                break;
            }
        }
    }
    // No header redirect here! The AJAX on the main page handles the refresh.
    exit; 
}
?>