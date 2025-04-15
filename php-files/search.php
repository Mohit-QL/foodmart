<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

$search = '';
$products = [];

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search = $conn->real_escape_string(trim($_GET['search']));
    $query = "SELECT * FROM products WHERE product_name LIKE '%$search%' OR product_description LIKE '%$search%'";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

?>

<div class="container py-5">
    <h2 class="mb-4">Search Results for: "<?= htmlspecialchars($search) ?>"</h2>

    <?php if (count($products) > 0): ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?= $product['product_image'] ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($product['product_description']) ?></p>
                            <p class="text-success fw-bold"><?= number_format($product['product_price'], 2) ?> USD</p>
                            <a href="#" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No products found matching your search.</p>
    <?php endif; ?>
</div>

