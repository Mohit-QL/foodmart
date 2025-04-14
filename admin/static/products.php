<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$result = $conn->query("SELECT p.id, p.product_name, p.product_description, p.product_price, p.old_price, p.product_size, p.product_image, u.name as user_name FROM products p JOIN users u ON p.user_id = u.id");
?>

<?php
include './header.php';
?>
<div class="container py-5">

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
                <th>Price</th>
                <th>Old Price</th>
                <th>Size</th>
                <th>Added By</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                    <td><?= htmlspecialchars($product['product_description']) ?></td>
                    <td><?= number_format($product['product_price'], 2) ?> USD</td>
                    <td><?= $product['old_price'] ? number_format($product['old_price'], 2) . ' USD' : 'N/A' ?></td>
                    <td><?= htmlspecialchars($product['product_size']) ?></td>
                    <td><?= htmlspecialchars($product['user_name']) ?></td>
                    <td>
                        <?php if ($product['product_image']): ?>
                            <img src="<?= $product['product_image'] ?>" alt="Product Image" width="100">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


<?php
include './footer.php';
?>