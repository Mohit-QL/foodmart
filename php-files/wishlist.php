<?php
session_start();
include '../php-files/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("
    SELECT p.*, w.id AS wishlist_id
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE w.user_id = ?
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Wishlist</title>
  <link rel="stylesheet" href="your-styles.css">
</head>
<body>
  <section class="py-5">
    <div class="container">
      <h2 class="mb-4">My Wishlist</h2>
      <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
              <img src="admin/static/<?= $row['product_image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($row['product_name']) ?>" style="height: 200px; object-fit: contain;">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($row['product_name']) ?></h5>
                <p class="card-text">$<?= number_format($row['product_price'], 2) ?></p>
                <a href="add_to_cart.php?pid=<?= $row['id'] ?>" class="btn btn-sm btn-success">Add to Cart</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>
</body>
</html>
