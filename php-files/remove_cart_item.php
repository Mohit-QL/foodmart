<?php
session_start();
include '../php-files/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id']; 

if (isset($_GET['user_id']) && isset($_GET['product_id']) && is_numeric($_GET['user_id']) && is_numeric($_GET['product_id'])) {
    $user_id = (int) $_GET['user_id'];  
    $product_id = (int) $_GET['product_id'];  

    $check_query = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $check_query->bind_param("ii", $user_id, $product_id);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows > 0) {
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $delete_stmt->bind_param("ii", $user_id, $product_id);

        if ($delete_stmt->execute()) {
            header('Location: ../index.php'); 
            exit;
        } else {
            echo "Error removing item from cart.";
        }
    } else {
        echo "This product is not in your cart or has already been removed.";
    }
} else {
    echo "Invalid product or user ID.";
}
?>
