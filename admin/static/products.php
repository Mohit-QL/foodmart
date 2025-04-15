<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$result = $conn->query("SELECT p.id, p.product_name, p.product_description, p.product_price, p.old_price, p.product_size, u.name as user_name, c.category_name FROM products p JOIN users u ON p.user_id = u.id JOIN categories c ON p.category_id = c.id");
?>

<?php include './header.php'; ?>

<style>
    .description-truncate {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        max-height: 4.5em;
        line-height: 1.5em;
    }
</style>

<div class="container py-5" style="max-width: 1300px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-4">Products</h2>
        <a href="add-product.php" class="btn btn-success">+ Add Product</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Old Price</th>
                <th>Size</th>
                <th>Added By</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                    <td>
                        <div class="description-truncate">
                            <?= htmlspecialchars($product['product_description']) ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></td>
                    <td><?= number_format($product['product_price'], 2) ?> USD</td>
                    <td><?= $product['old_price'] ? number_format($product['old_price'], 2) . ' USD' : 'N/A' ?></td>
                    <td><?= htmlspecialchars($product['product_size']) ?></td>
                    <td><?= htmlspecialchars($product['user_name']) ?></td>
                    <td>
                        <?php
                        $image_result = $conn->query("SELECT image_path FROM product_images WHERE product_id = " . $product['id'] . " LIMIT 1");
                        if ($image = $image_result->fetch_assoc()):
                        ?>
                            <img src="<?= $image['image_path'] ?>" alt="Product Image" width="100">
                        <?php else: ?>
                            <span class="text-muted">No image</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary mb-2 w-100">Edit</a>
                        <a href="delete-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger w-100" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include './footer.php'; ?>