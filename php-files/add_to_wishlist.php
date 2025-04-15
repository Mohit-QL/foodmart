<?php
session_start();
include '../php-files/db.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['pid']);

$stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $insert = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $product_id);
    $insert->execute();
}

header("Location: wishlist.php");
