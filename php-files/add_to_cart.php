<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../php-files/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = (int) $_GET['product_id'];

    $check = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id");
    } else {
        $product_query = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $product_query->bind_param("i", $product_id);
        $product_query->execute();
        $product = $product_query->get_result()->fetch_assoc();


        $image_query = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ? LIMIT 1");
        $image_query->bind_param("i", $product_id);
        $image_query->execute();
        $image_result = $image_query->get_result();
        $image = $image_result->fetch_assoc();

        $image_path = str_replace("productsuploads/products", "products", $image['image_path']);


        if ($product && $image) {
            $name = $product['product_name'];
            $price = $product['product_price'];
          
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, name, price, image, quantity) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("iisss", $user_id, $product_id, $name, $price, $image_path);
            $stmt->execute();
          }
    }
}

header('Location: ../index.php');
exit;
