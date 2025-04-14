<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['role'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $image_name = "";
        if (!empty($_FILES['image']['name'])) {
            $image_name = time() . "_" . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image_name);
        }

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, image, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $image_name, $role);
        $stmt->execute();

        if ($role == 'admin') {
            header("Location: index.php");
        } elseif ($role == 'user') {
            header("Location: ../../index.php");
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sign Up | FoodMart</title>
    <link href="css/app.css" rel="stylesheet">
</head>

<body>
    <main class="d-flex w-100">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">
                        <div class="text-center mt-4">
                            <h1 class="h2">Get started</h1>
                            <p class="lead">Create your account.</p>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">Full name</label>
                                        <input type="text" name="name" class="form-control form-control-lg" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control form-control-lg" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control form-control-lg" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Profile Image</label>
                                        <input type="file" name="image" class="form-control form-control-lg" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Select Role</label>
                                        <select name="role" class="form-control form-control-lg" required>
                                            <option value="admin">Admin</option>
                                            <option value="user">User</option>
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <button type="submit" class="btn btn-lg btn-primary">Sign up</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            Already have an account? <a href="sign-in.php">Log In</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>