<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$product_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

header("Location: products.php");
exit;
?>
