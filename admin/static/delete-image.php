<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit;
}

if (isset($_GET['product_id']) && isset($_GET['image_id'])) {
    $product_id = intval($_GET['product_id']);
    $image_id = intval($_GET['image_id']);

    $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE id = ? AND product_id = ?");
    $stmt->bind_param("ii", $image_id, $product_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($image_path);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $delete_stmt = $conn->prepare("DELETE FROM product_images WHERE id = ? AND product_id = ?");
        $delete_stmt->bind_param("ii", $image_id, $product_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }

    $stmt->close();
}

header("Location: edit-product.php?id=" . $product_id);
exit;
