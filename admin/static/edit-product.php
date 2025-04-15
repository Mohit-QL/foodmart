<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit;
}

$product_id = $_GET['id'];

$result = $conn->query("SELECT * FROM products WHERE id = $product_id");
$product = $result->fetch_assoc();

$image_result = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $old_price = $_POST['old_price'];
    $product_size = $_POST['product_size'];
    $category_id = $_POST['category_id'];

    $stmt = $conn->prepare("UPDATE products SET product_name = ?, product_description = ?, product_price = ?, old_price = ?, product_size = ?, category_id = ? WHERE id = ?");
    $stmt->bind_param("ssddsis", $product_name, $product_description, $product_price, $old_price, $product_size, $category_id, $product_id);
    $stmt->execute();

    if (isset($_FILES['product_images']) && count($_FILES['product_images']['name']) > 0) {
        $image_count = count($_FILES['product_images']['name']);
        for ($i = 0; $i < $image_count; $i++) {
            if ($_FILES['product_images']['error'][$i] === UPLOAD_ERR_OK) {
                $image_tmp_name = $_FILES['product_images']['tmp_name'][$i];
                $image_name = basename($_FILES['product_images']['name'][$i]);
                $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

                if (in_array($image_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $image_new_name = uniqid('product_', true) . '.' . $image_extension;
                    $image_upload_path = 'uploads/products/' . $image_new_name;

                    if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                        $image_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                        $image_stmt->bind_param("is", $product_id, $image_upload_path);
                        $image_stmt->execute();
                    }
                } else {
                    echo "Invalid image type. Only JPG, JPEG, PNG, and GIF are allowed.";
                    exit;
                }
            }
        }
    }

    header("Location: products.php");
    exit;
}

$categories_result = $conn->query("SELECT * FROM categories");
?>

<?php
include './header.php';
?>

<div class="container py-4">
    <h2 class="mb-4">Edit Product</h2>

    <form method="POST" action="edit-product.php?id=<?= $product['id'] ?>" enctype="multipart/form-data">
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

        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['category_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Images</label>
            <div class="d-flex flex-wrap">
                <?php while ($image = $image_result->fetch_assoc()): ?>
                    <div class="position-relative me-3 mb-3" style="width: 100px;">
                        <img src="<?= $image['image_path'] ?>" alt="Product Image" width="100" class="img-thumbnail">
                        <a href="delete-image.php?product_id=<?= $product['id'] ?>&image_id=<?= $image['id'] ?>"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0"
                            onclick="return confirm('Are you sure you want to delete this image?')">X</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>


        <div class="mb-3">
            <label for="product_images" class="form-label">Upload New Images (max 5)</label>
            <input type="file" name="product_images[]" id="product_images" class="form-control" accept="image/*" multiple>
        </div>

        <button type="submit" class="btn btn-success">Update Product</button>
        <a href="products.php" class="btn btn-link text-success">Cancel</a>
    </form>
</div>

<?php
include './footer.php';
?>