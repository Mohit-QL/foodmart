<?php
session_start();
include './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['role'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $image_name = "";
        if (!empty($_FILES['image']['name'])) {
            $image_name = time() . "_" . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads" . $image_name);
        }

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, image, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $image_name, $role);
        $stmt->execute();

        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - FoodMart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/vendor.css">
    <link rel="stylesheet" type="text/css" href="../style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&family=Open+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <style>
        form {
            
            padding: 30px;
            border-radius: 10px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #D8D8D8;
            border-radius: 5px;
        }

        button {
            background-color: #A3BE4C;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #FFC43F;
        }

        .wrapper {
            width: 100%;
        }

        .register-section {
            background: url('images/bg-leaves-img-pattern.png') no-repeat right center;
            background-size: contain;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            max-width: 900px;
            width: 100%;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .form-side,
        .info-side {
            padding: 40px;
        }

        a{
            color: #A3BE4C;

        }

        .info-side h2 {
            color: #FFC43F;
        }
        .logo{
            position: absolute;
            top: 7%;
            left: 27%;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <section class="register-section">
            <img src="../images/logo.png" alt="" class="logo">
            <div class="container form-container d-flex flex-wrap">
         
                <div class="col-md-12 form-side">
                    <form method="POST" enctype="multipart/form-data">
                        <h2 style="color: #ffc43f;">Register</h2>
                        <input type="text" name="name" placeholder="Full Name" required>
                        <input type="email" name="email" placeholder="Email Address" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <select name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <input type="file" name="image" accept="image/*">
                        <button type="submit">Register</button>
                        <p class="mt-3">Alredy Register? <a href="login.php">Login</a></p>
                    </form>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
