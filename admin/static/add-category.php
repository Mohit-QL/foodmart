<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name']);
    
    if (!empty($category_name)) {
        $check = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
        $check->bind_param("s", $category_name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = '<div class="alert alert-danger">Category already exists!</div>';
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->bind_param("s", $category_name);
            $stmt->execute();
            $stmt->close();
            $message = '<div class="alert alert-success">Category added successfully!</div>';
        }

        $check->close();
    } else {
        $message = '<div class="alert alert-danger">Category name is required.</div>';
    }
}

include './header.php';
?>

<div class="container py-5">
    <h2 class="mb-4">Add New Category</h2>

    <?= $message ?>

    <form method="POST" class="border p-4 bg-light rounded" style="max-width: 500px;">
        <div class="mb-3">
            <label for="category_name" class="form-label">Category Name</label>
            <input type="text" name="category_name" id="category_name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Add Category</button>
    </form>
    <a href="category.php" class="btn btn-link ps-4 pt-3 text-success">Back to Categories</a>
</div>

<?php include './footer.php'; ?>
