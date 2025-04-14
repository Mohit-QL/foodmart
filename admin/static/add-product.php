<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $old_price = $_POST['old_price'];
    $product_size = $_POST['product_size'];
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['product_image']['tmp_name'];
        $image_name = basename($_FILES['product_image']['name']);
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (in_array($image_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $image_new_name = uniqid('product_', true) . '.' . $image_extension;
            $image_upload_path = 'uploads/products/' . $image_new_name;

            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, product_price, old_price, product_size, user_id, product_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssddsss", $product_name, $product_description, $product_price, $old_price, $product_size, $user_id, $image_upload_path);
                $stmt->execute();
            } else {
                echo "Error uploading image.";
                exit;
            }
        } else {
            echo "Invalid image type. Only JPG, JPEG, PNG, and GIF are allowed.";
            exit;
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, product_price, old_price, product_size, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddds", $product_name, $product_description, $product_price, $old_price, $product_size, $user_id);
        $stmt->execute();
    }

    header("Location: products.php");
    exit;
}
?>

<?php
include './header.php';
?>
<div class="container py-4">
    <h2 class="mb-4">Add New Product</h2>

    <form method="POST" action="add-product.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" name="product_name" id="product_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="product_description" class="form-label">Description</label>
            <textarea name="product_description" id="product_description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="product_price" class="form-label">Price</label>
            <input type="number" name="product_price" id="product_price" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="old_price" class="form-label">Old Price</label>
            <input type="number" name="old_price" id="old_price" class="form-control" step="0.01">
        </div>
        <div class="mb-3">
            <label for="product_size" class="form-label">Size</label>
            <input type="text" name="product_size" id="product_size" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="product_image" class="form-label">Product Image</label>
            <input type="file" name="product_image" id="product_image" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-success">Add Product</button>
    </form>
</div>

<?php
include './footer.php';
?>