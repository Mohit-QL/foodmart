<?php
session_start();
$login_error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "foodmart");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $username;
            header("Location: ../index.php");
            exit;
        } else {
            $login_error = "Invalid credentials.";
        }
    } else {
        $login_error = "Invalid credentials.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - FoodMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/vendor.css">
    <link rel="stylesheet" type="text/css" href="../style.css">

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }

        .wrapper {
            width: 100%;
        }

        .login-section {
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

        .info-side {
            background: url('images/bg-leaves-img-pattern.png') no-repeat center;
            background-size: cover;
            color: white;
        }

        .info-side h2 {
            color: #FFC43F;
        }

        form {

            padding: 30px;


        }

        input {
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
            font-weight: bold;
        }

        button:hover {
            background-color: #FFC43F;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        a {
            color: #A3BE4C;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .logo {
            position: absolute;
            top: 20%;
            left: 27%;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <section class="login-section">
            <img src="../images/logo.png" alt="" class="logo">
            <div class="container form-container d-flex flex-wrap">

                <!-- Login Form -->
                <div class="col-md-12 form-side">
                    <form method="post">
                        <h2 style="color: #ffc43f;">Login</h2>
                        <?php if ($login_error): ?>
                            <p class="error"><?= $login_error ?></p>
                        <?php endif; ?>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <button type="submit">Login</button>
                        <p class="mt-3">Don't have an account? <a href="register.php">Register</a></p>
                    </form>
                </div>
            </div>
        </section>
    </div>


</body>

</html>