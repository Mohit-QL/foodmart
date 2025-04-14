<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['email'], $_POST['password'])) {
		$email = $_POST['email'];
		$password = $_POST['password'];

		$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows === 1) {
			$user = $result->fetch_assoc();
			if (password_verify($password, $user['password'])) {
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_name'] = $user['name'];
				$_SESSION['user_role'] = $user['role'];
				$_SESSION['user_image'] = $user['image'];
				header("Location: ./index.php");
				exit;
			} else {
				$error = "Incorrect password!";
			}
		} else {
			$error = "User not found!";
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Sign In | FoodMart</title>
	<link href="css/app.css" rel="stylesheet">
</head>

<body>
	<main class="d-flex w-100">
		<div class="container d-flex flex-column">
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">
						<div class="text-center mt-4">
							<h1 class="h2">Welcome back!</h1>
							<p class="lead">Sign in to your account</p>
						</div>
						<div class="card">
							<div class="card-body">
								<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
								<form method="POST">
									<div class="mb-3">
										<label class="form-label">Email</label>
										<input type="email" name="email" class="form-control form-control-lg" required>
									</div>
									<div class="mb-3">
										<label class="form-label">Password</label>
										<input type="password" name="password" class="form-control form-control-lg" required>
									</div>
									<div class="d-grid gap-2 mt-3">
										<button type="submit" class="btn btn-lg btn-primary">Sign in</button>
									</div>
								</form>
							</div>
						</div>
						<div class="text-center mb-3">
							Don't have an account? <a href="sign-up.php">Sign up</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</body>
</html>
