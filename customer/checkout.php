<?php
include('csession.php');
include('../sql.php');

if (!isset($_SESSION['customer_login_user']) || empty($_SESSION["shopping_cart"])) {
    header("location: cbuy_crops.php");
    exit;
}

$user = $_SESSION['customer_login_user'];
$total_price = $_SESSION['Total_Cart_Price'];
$order_date = date("Y-m-d H:i:s");

// 1. Start a Transaction (ensures data safety)
mysqli_begin_transaction($conn);

try {
    foreach ($_SESSION["shopping_cart"] as $item) {
        $name = $item['item_name'];
        $qty = $item['item_quantity'];
        $price = $item['item_price'];
        
        // Insert into your permanent orders/history table
        // Adjust table names/columns to match your database
        $sql = "INSERT INTO orders (customer_user, crop_name, quantity, total_price, order_date) 
                VALUES ('$user', '$name', '$qty', '$price', '$order_date')";
        mysqli_query($conn, $sql);
        
        // 2. Reduce the stock in production_approx
        $update_stock = "UPDATE production_approx SET quantity = quantity - $qty WHERE crop = '$name'";
        mysqli_query($conn, $update_stock);
    }

    // 3. Clear the temporary cart table for this user
    // (If your cart table doesn't have a user column, this deletes everything)
    mysqli_query($conn, "DELETE FROM `cart` WHERE 1");

    // 4. Commit changes
    mysqli_commit($conn);

    // 5. Clear the Session Cart
    unset($_SESSION["shopping_cart"]);
    unset($_SESSION["Total_Cart_Price"]);

    $success = true;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include ('cheader.php'); ?>
    <style>
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-top: 100px;
        }
        .check-icon { font-size: 80px; color: #2D6A4F; margin-bottom: 20px; }
        .btn-home { background: #2D6A4F; color: white; padding: 12px 30px; border-radius: 10px; text-decoration: none; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body style="background: #f0f2f5;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="success-card">
                    <?php if($success): ?>
                        <div class="check-icon">✔</div>
                        <h2>Order Placed Successfully!</h2>
                        <p>Your fresh produce will be processed soon.</p>
                        <a href="cbuy_crops.php" class="btn-home">Return to Market</a>
                    <?php else: ?>
                        <h2 class="text-danger">Oops! Something went wrong.</h2>
                        <p>Please try again or contact support.</p>
                        <a href="cbuy_crops.php" class="btn-home">Back to Cart</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>