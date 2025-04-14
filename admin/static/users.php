<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

// Redirect if not admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$result = $conn->query("SELECT id, name, email, role, image FROM users");
?>




<?php
include './header.php'
?>

<main class="content">
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Registered Users</h2>
            <a href="sign-up.php" class="btn btn-success">+ Add New User</a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td>
                            <img src="uploads/<?= htmlspecialchars($user['image'] ?? 'default.jpg') ?>"
                                onerror="this.src='uploads/default.jpg';"
                                width="40" height="40" class="rounded-circle" alt="Profile">

                        </td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge <?= $user['role'] === 'admin' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
include './footer.php'
?>