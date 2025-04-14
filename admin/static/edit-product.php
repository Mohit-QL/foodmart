<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $old_price = $_POST['old_price'];
    $product_size = $_POST['product_size'];

    $stmt = $conn->prepare("UPDATE products SET product_name = ?, product_description = ?, product_price = ?, old_price = ?, product_size = ? WHERE id = ?");
    $stmt->bind_param("ssddsi", $product_name, $product_description, $product_price, $old_price, $product_size, $product_id);
    $stmt->execute();

    header("Location: products.php");
    exit;
}

$product_id = $_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = $product_id");
$product = $result->fetch_assoc();
?>


<?php
include './header.php';
?>

<div class="container py-4">
    <h2 class="mb-4">Edit Product</h2>

    <form method="POST" action="edit-product.php">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" name="product_name" id="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="product_description" class="form-label">Description</label>
            <textarea name="product_description" id="product_description" class="form-control" required><?= htmlspecialchars($product['product_description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="product_price" class="form-label">Price</label>
            <input type="number" name="product_price" id="product_price" class="form-control" value="<?= $product['product_price'] ?>" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="old_price" class="form-label">Old Price</label>
            <input type="number" name="old_price" id="old_price" class="form-control" value="<?= $product['old_price'] ?>" step="0.01">
        </div>
        <div class="mb-3">
            <label for="product_size" class="form-label">Size</label>
            <input type="text" name="product_size" id="product_size" class="form-control" value="<?= htmlspecialchars($product['product_size']) ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update Product</button>
    </form>
</div>

<?php
include './footer.php';
?>