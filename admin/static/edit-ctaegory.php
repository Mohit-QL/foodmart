<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: category.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['category_name'];
    $update = $conn->prepare("UPDATE categories SET category_name = ? WHERE id = ?");
    $update->bind_param("si", $new_name, $id);
    $update->execute();
    header("Location: category.php");
    exit;
}

include './header.php';
?>

<div class="container py-5">
    <h2>Edit Category</h2>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="category_name" value="<?= htmlspecialchars($category['category_name']) ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Update Category</button>
        <a href="category.php" class="btn btn-link text-success">Cancel</a>
    </form>
</div>


<?php include './footer.php'; ?>