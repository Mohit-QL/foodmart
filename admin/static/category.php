<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $message = '<div class="alert alert-success">Category added successfully!</div>';
    } elseif ($_GET['msg'] === 'updated') {
        $message = '<div class="alert alert-info">Category updated successfully!</div>';
    } elseif ($_GET['msg'] === 'deleted') {
        $message = '<div class="alert alert-warning">Category deleted.</div>';
    }
}

$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<?php include './header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Categories</h2>
        <a href="add-category.php" class="btn btn-success">+ Add Category</a>
    </div>

    <?= $message ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $category['id'] ?></td>
                        <td><?= htmlspecialchars($category['category_name']) ?></td>
                        <td>
                            <a href="edit-ctaegory.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete-category.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include './footer.php'; ?>
