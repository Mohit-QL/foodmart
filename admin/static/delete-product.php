<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['image_id'])) {
    $image_id = $_GET['image_id'];
    $product_id = $_GET['product_id'];

    $image_result = $conn->query("SELECT image_path FROM product_images WHERE id = $image_id");
    $image = $image_result->fetch_assoc();

    if ($image) {
        $image_path = $image['image_path'];

        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $stmt = $conn->prepare("DELETE FROM product_images WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
    }

    header("Location: edit-product.php?id=$product_id");
    exit;

} elseif (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $image_result = $conn->query("SELECT image_path FROM product_images WHERE product_id = $product_id");

    while ($image = $image_result->fetch_assoc()) {
        $image_path = $image['image_path'];

        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $stmt = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    header("Location: products.php");
    exit;
}

?>
