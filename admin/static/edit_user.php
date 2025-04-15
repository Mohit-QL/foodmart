<?php
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_GET['id'])) {
    header("Location: admin_user.php");
    exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']);

    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = "uploads/" . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        mysqli_query($conn, "UPDATE users SET name='$name', email='$email', role='$role', image='$image_name' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE users SET name='$name', email='$email', role='$role' WHERE id=$id");
    }

    header("Location: users.php");
    exit;
}

?>

<?php
include './header.php';

?>


<main class="d-flex w-100">
    <div class="container d-flex flex-column">
        <div class="row vh-100">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                <div class="d-table-cell pt-5">
                    <div class="text-center mt-4">
                        <h1 class="h2">Edit User</h1>
                        <p class="lead">Modify user information below</p>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3 text-center">
                                    <img src="uploads/<?= htmlspecialchars($user['image'] ?? 'default.jpg') ?>"
                                        onerror="this.src='uploads/default.jpg';"
                                        width="80" height="80" class="rounded-circle mb-2" alt="User Profile">
                                    <input class="form-control" type="file" name="image" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control form-control-lg"
                                        value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-control form-control-lg" required>
                                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-lg btn-primary">Update User</button>
                                    <a href="admin_user.php" class="btn btn-lg btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mb-3">
                        <small>Back to <a href="users.php">User List</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include './footer.php';
?>