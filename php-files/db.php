<?php

$conn = new mysqli("localhost", "root", "", "foodmart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
