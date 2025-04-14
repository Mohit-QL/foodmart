<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'admin') {
	header("Location: http://localhost/foodmart/");
	exit;
}
?>

<?php
include './header.php';
?>


<main class="content">
	<div class="container-fluid p-0">

		<div class="mb-3">
			<h1 class="h3 d-inline align-middle">Profile</h1>

		</div>
		<div class="row">
			<div class="col-md-4 col-xl-3">
				<div class="card mb-3">
					<div class="card-header">
						<h5 class="card-title mb-0">Profile Details</h5>
					</div>
					<div class="card-body text-center">
						<img src="uploads/<?php echo htmlspecialchars($_SESSION['user_image'] ?? 'default.jpg'); ?>"
							alt="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>"
							class="img-fluid rounded-circle mb-2" width="128" height="128" />

						<h5 class="card-title mb-0">
							<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
						</h5>

						<div class="text-muted mb-2">
							<?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Member'); ?>
						</div>

						<div>
							<a class="btn btn-primary btn-sm" href="#">Edit</a>
							<a class="btn btn-primary btn-sm" href="#"><span></span>Delete</a>
						</div>
					</div>

					<hr class="my-0" />

				</div>
			</div>


		</div>

	</div>
</main>

<?php

include './footer.php';
?>