<?php
$conn = new mysqli("localhost", "root", "", "foodmart");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
}

header("Location: users.php");
