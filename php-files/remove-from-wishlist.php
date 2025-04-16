<?php
session_start();
include './db.php';

if (isset($_GET['product_id']) && isset($_SESSION['user_id'])) {
    $product_id = $_GET['product_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    
    if ($stmt->execute()) {
        header("Location: wishlist.php");
        exit();
    } else {
        echo "Error removing product from wishlist.";
    }
}
?>
