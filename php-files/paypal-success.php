<?php
session_start();
include '../php-files/db.php';

if (isset($_GET['orderID']) && isset($_GET['payerID'])) {
    $orderID = mysqli_real_escape_string($conn, $_GET['orderID']);
    $payerID = mysqli_real_escape_string($conn, $_GET['payerID']);
    $user_id = $_SESSION['user_id'];

    $total = 0;
    $cart_query = mysqli_query($conn, "SELECT price, quantity FROM cart WHERE user_id = $user_id");
    while ($row = mysqli_fetch_assoc($cart_query)) {
        $total += $row['price'] * $row['quantity'];
    }

    $query = "INSERT INTO orders (user_id, order_id, payer_id, total_amount, status, date) 
              VALUES ('$user_id', '$orderID', '$payerID', '$total', 'Completed', NOW())";

    if (mysqli_query($conn, $query)) {
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
        echo "<h3>Payment Successful!</h3>";
        echo "<p>Order ID: $orderID</p>";
        echo "<p>Payer ID: $payerID</p>";
        header("Location: order-summary.php?orderID=$orderID");
        exit;
    } else {
        echo "<strong>MySQL Error:</strong> " . mysqli_error($conn); 
        echo "<h3>Something went wrong. Please try again later.</h3>";
    }
} else {
    echo "<h3>Invalid Payment Details</h3>";
}
