<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include '../php-files/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("
    SELECT p.*, w.id AS wishlist_id
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE w.user_id = ?
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();


$conn = new mysqli("localhost", "root", "", "foodmart");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user_image = $_SESSION['user_image'] ?? 'default.png';
$user_name = $_SESSION['user_name'] ?? 'Guest User';
$user_email = $_SESSION['user_email'] ?? 'guest@example.com';
$user_phone = $_SESSION['user_phone'] ?? 'N/A';
$user_address = $_SESSION['user_address'] ?? 'Not provided';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $cart_items = $result->fetch_all(MYSQLI_ASSOC);
} else {
  $cart_items = [];
}

$subtotal = 0;
foreach ($cart_items as $item) {
  $subtotal += $item['price'] * $item['quantity'];
}

$tax = $subtotal * 0.10;

$discount = 0.00;

$total = $subtotal + $tax - $discount;

$formatted_subtotal = number_format($subtotal, 2);
$formatted_tax = number_format($tax, 2);
$formatted_discount = number_format($discount, 2);
$formatted_total = number_format($total, 2);



if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'] ?? 0;
$cart_items = [];
$total_price = 0;

if ($user_id) {
  $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'];
  }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>My Profile - FoodMart</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="./vendor.css">
  <link rel="stylesheet" type="text/css" href="Foodmart/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


  <style>
    .wishlist-item {
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid #eee;
    }

    .item-container {
      width: 280px;
      height: fit-content;
      display: flex;
      gap: 20px;
    }

    .item-image {
      position: relative;

    }

    .item-image img {
      width: 280px;
      height: 287px;
      object-fit: contain;
      border-radius: 5px;
    }

    .discount {
      position: absolute;
      top: 5px;
      left: 5px;
      background: red;
      color: white;
      font-weight: bold;
      padding: 2px 5px;
      border-radius: 3px;
      font-size: 12px;
    }

    .item-details {
      flex: 1;
    }

    .price .original-price {
      text-decoration: line-through;
      color: #999;
      margin-left: 10px;
    }

    .add-to-cart {
      background: #007bff;
      color: white;
      border: none;
      padding: 5px 15px;
      border-radius: 3px;
      cursor: pointer;
      margin-top: 10px;
    }

    .notify-btn {
      background: #6c757d;
      color: white;
      border: none;
      padding: 5px 15px;
      border-radius: 3px;
      cursor: pointer;
      margin-top: 10px;
    }

    .out-of-stock {
      opacity: 0.7;
    }

    .rating {
      color: gold;
      margin: 5px 0;
    }

    h3 {
      margin: 0 0 5px 0;
      font-size: 16px;
    }

    .remove a {
      font-size: 12px;
      color: #666 !important;
      transition: color 0.2s ease-in-out;
    }

    .remove a:hover {
      color: red !important;
    }

    .card-body h5 {
      font-weight: bold;
    }

    .cart-item img {
      width: 70px;
      height: 70px;
      object-fit: cover;
    }

    .btn-outline {
      border: 1px solid #ccc;
    }

    .cart-summary {
      background: #f9f9f9;
      padding: 20px;
      border-radius: 10px;
    }

    .coupon input {
      border-radius: 5px 0 0 5px;
    }

    .coupon button {
      border-radius: 0 5px 5px 0;
    }

    .d-link {
      padding: 8px 10px;
      color: #000;
    }

    .d-link:hover {
      background-color: #FCF7EB;
      padding: 8px 10px;
      color: #FFC43F;
    }

    .profile-box-1 {
      height: fit-content;
      width: 250px;
      background-color: white;
      border: 1px solid gray;
      border-radius: 10px;
      padding: 15px;
    }

    body {
      background-color: #f9fafb;
    }

    .sidebar {
      border-radius: 20px;
      background-color: #fff;
      padding: 30px 20px;
    }

    .sidebar .nav-link {
      font-size: 16px;
      padding: 10px;
      border-radius: 10px;
    }

    .sidebar .nav-link.active {
      background-color: #0046d4;
      color: white !important;
    }

    .profile-img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
    }

    .profile-box {
      background: #fff;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    .profile-box label {
      font-weight: 500;
      margin-bottom: 5px;
      color: #333;
    }

    .profile-box p {
      font-size: 15px;
      color: #555;
    }

    .profile {
      background-color: #e6f3fa;
      color: black;
      width: 250px;
      height: 100px;
      border: 1px solid #FFC43F;
      inset: 14px auto auto 0px !important;
    }

    .profile a {
      text-decoration: none;
    }

    .category-box {
      width: 150px;
      height: 170px;
      background-color: #f8f8f8;
      border-radius: 12px;
      padding: 20px 10px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .category-title {
      font-size: 16px;
      font-weight: 600;
      color: #333;
      margin: 0;
    }

    .color-option {
      width: 25px;
      height: 25px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 5px;
      border: 2px solid #ccc;
      cursor: pointer;
    }

    .color-option.black {
      background-color: #000;
    }

    .color-option.gray {
      background-color: #666;
    }

    .color-option.blue {
      background-color: #3B5998;
    }

    .color-option.pink {
      background-color: #C71585;
    }

    .thumb-img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      margin: 10px;
      cursor: pointer;
      margin-right: 15px;
    }

    .guarantee i {
      color: #A3BE4C;
      margin-right: 5px;
    }

    .profile {
      background-color: #e6f3fa;
      color: black;
      width: 250px;
      height: 100px;
      border: 1px solid #FFC43F;
      inset: 14px auto auto 0px !important;
    }

    .profile a {
      text-decoration: none;
    }

    .category-box {
      width: 150px;
      height: 170px;
      background-color: #f8f8f8;
      border-radius: 12px;
      padding: 20px 10px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .category-title {
      font-size: 16px;
      font-weight: 600;
      color: #333;
      margin: 0;
    }

    .zoom-container {
      position: relative;
      overflow: hidden;
      width: 100%;
      height: 400px;
    }

    .zoom-result {
      display: none;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: 1px solid #ccc;
      background-color: rgba(255, 255, 255, 0.7);
      z-index: 10;
      /* background-repeat: no-repeat; */
    }

    .thumb-img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      margin: 5px;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .thumb-img:hover {
      transform: scale(1.1);
    }

    .main-image {
      cursor: zoom-in;
    }


    .main-image {
      cursor: zoom-in;
    }

    .badge {
      background-color: red !important;
      color: white;
      padding: 5px;
      font-size: 12px;
      border-radius: 50%;
      position: absolute;
      top: -5px;
      right: -10px;
    }

    .custom-width {
      width: 250px !important;
    }

    .profile {
      background-color: #e6f3fa;
      color: black;
      width: 250px;
      height: 100px;
      border: 1px solid #FFC43F;
      inset: 14px auto auto 0px !important;
    }

    .profile a {
      text-decoration: none;
    }

    .category-box {
      width: 150px;
      height: 170px;
      background-color: #f8f8f8;
      border-radius: 12px;
      padding: 20px 10px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .category-title {
      font-size: 16px;
      font-weight: 600;
      color: #333;
      margin: 0;
    }
  </style>
</head>

<body>

  <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <defs>
      <symbol xmlns="http://www.w3.org/2000/svg" id="link" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12 19a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm5 0a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm0-4a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm-5 0a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm7-12h-1V2a1 1 0 0 0-2 0v1H8V2a1 1 0 0 0-2 0v1H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3Zm1 17a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-9h16Zm0-11H4V6a1 1 0 0 1 1-1h1v1a1 1 0 0 0 2 0V5h8v1a1 1 0 0 0 2 0V5h1a1 1 0 0 1 1 1ZM7 15a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm0 4a1 1 0 1 0-1-1a1 1 0 0 0 1 1Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="arrow-right" viewBox="0 0 24 24">
        <path fill="currentColor" d="M17.92 11.62a1 1 0 0 0-.21-.33l-5-5a1 1 0 0 0-1.42 1.42l3.3 3.29H7a1 1 0 0 0 0 2h7.59l-3.3 3.29a1 1 0 0 0 0 1.42a1 1 0 0 0 1.42 0l5-5a1 1 0 0 0 .21-.33a1 1 0 0 0 0-.76Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="category" viewBox="0 0 24 24">
        <path fill="currentColor" d="M19 5.5h-6.28l-.32-1a3 3 0 0 0-2.84-2H5a3 3 0 0 0-3 3v13a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3Zm1 13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-13a1 1 0 0 1 1-1h4.56a1 1 0 0 1 .95.68l.54 1.64a1 1 0 0 0 .95.68h7a1 1 0 0 1 1 1Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="calendar" viewBox="0 0 24 24">
        <path fill="currentColor" d="M19 4h-2V3a1 1 0 0 0-2 0v1H9V3a1 1 0 0 0-2 0v1H5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3Zm1 15a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7h16Zm0-9H4V7a1 1 0 0 1 1-1h2v1a1 1 0 0 0 2 0V6h6v1a1 1 0 0 0 2 0V6h2a1 1 0 0 1 1 1Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="heart" viewBox="0 0 24 24">
        <path fill="currentColor" d="M20.16 4.61A6.27 6.27 0 0 0 12 4a6.27 6.27 0 0 0-8.16 9.48l7.45 7.45a1 1 0 0 0 1.42 0l7.45-7.45a6.27 6.27 0 0 0 0-8.87Zm-1.41 7.46L12 18.81l-6.75-6.74a4.28 4.28 0 0 1 3-7.3a4.25 4.25 0 0 1 3 1.25a1 1 0 0 0 1.42 0a4.27 4.27 0 0 1 6 6.05Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="plus" viewBox="0 0 24 24">
        <path fill="currentColor" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="minus" viewBox="0 0 24 24">
        <path fill="currentColor" d="M19 11H5a1 1 0 0 0 0 2h14a1 1 0 0 0 0-2Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="cart" viewBox="0 0 24 24">
        <path fill="currentColor" d="M8.5 19a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 8.5 19ZM19 16H7a1 1 0 0 1 0-2h8.491a3.013 3.013 0 0 0 2.885-2.176l1.585-5.55A1 1 0 0 0 19 5H6.74a3.007 3.007 0 0 0-2.82-2H3a1 1 0 0 0 0 2h.921a1.005 1.005 0 0 1 .962.725l.155.545v.005l1.641 5.742A3 3 0 0 0 7 18h12a1 1 0 0 0 0-2Zm-1.326-9l-1.22 4.274a1.005 1.005 0 0 1-.963.726H8.754l-.255-.892L7.326 7ZM16.5 19a1.5 1.5 0 1 0 1.5 1.5a1.5 1.5 0 0 0-1.5-1.5Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="check" viewBox="0 0 24 24">
        <path fill="currentColor" d="M18.71 7.21a1 1 0 0 0-1.42 0l-7.45 7.46l-3.13-3.14A1 1 0 1 0 5.29 13l3.84 3.84a1 1 0 0 0 1.42 0l8.16-8.16a1 1 0 0 0 0-1.47Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="trash" viewBox="0 0 24 24">
        <path fill="currentColor" d="M10 18a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1ZM20 6h-4V5a3 3 0 0 0-3-3h-2a3 3 0 0 0-3 3v1H4a1 1 0 0 0 0 2h1v11a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8h1a1 1 0 0 0 0-2ZM10 5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1h-4Zm7 14a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8h10Zm-3-1a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="star-outline" viewBox="0 0 15 15">
        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.5 9.804L5.337 11l.413-2.533L4 6.674l2.418-.37L7.5 4l1.082 2.304l2.418.37l-1.75 1.793L9.663 11L7.5 9.804Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="star-solid" viewBox="0 0 15 15">
        <path fill="currentColor" d="M7.953 3.788a.5.5 0 0 0-.906 0L6.08 5.85l-2.154.33a.5.5 0 0 0-.283.843l1.574 1.613l-.373 2.284a.5.5 0 0 0 .736.518l1.92-1.063l1.921 1.063a.5.5 0 0 0 .736-.519l-.373-2.283l1.574-1.613a.5.5 0 0 0-.283-.844L8.921 5.85l-.968-2.062Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="search" viewBox="0 0 24 24">
        <path fill="currentColor" d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="user" viewBox="0 0 24 24">
        <path fill="currentColor" d="M15.71 12.71a6 6 0 1 0-7.42 0a10 10 0 0 0-6.22 8.18a1 1 0 0 0 2 .22a8 8 0 0 1 15.9 0a1 1 0 0 0 1 .89h.11a1 1 0 0 0 .88-1.1a10 10 0 0 0-6.25-8.19ZM12 12a4 4 0 1 1 4-4a4 4 0 0 1-4 4Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="close" viewBox="0 0 15 15">
        <path fill="currentColor" d="M7.953 3.788a.5.5 0 0 0-.906 0L6.08 5.85l-2.154.33a.5.5 0 0 0-.283.843l1.574 1.613l-.373 2.284a.5.5 0 0 0 .736.518l1.92-1.063l1.921 1.063a.5.5 0 0 0 .736-.519l-.373-2.283l1.574-1.613a.5.5 0 0 0-.283-.844L8.921 5.85l-.968-2.062Z" />
      </symbol>
    </defs>
  </svg>

  <div class="preloader-wrapper">
    <div class="preloader">
    </div>
  </div>

  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart" aria-labelledby="My Cart">
    <div class="offcanvas-header justify-content-center">
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body ">
      <div class="order-md-last">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-primary">Your cart</span>
          <span class="badge bg-primary rounded-pill">3</span>
        </h4>
        <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between lh-sm">
            <div>
              <h6 class="my-0">Growers cider</h6>
              <small class="text-body-secondary">Brief description</small>
            </div>
            <span class="text-body-secondary">$12</span>
          </li>
          <li class="list-group-item d-flex justify-content-between lh-sm">
            <div>
              <h6 class="my-0">Fresh grapes</h6>
              <small class="text-body-secondary">Brief description</small>
            </div>
            <span class="text-body-secondary">$8</span>
          </li>
          <li class="list-group-item d-flex justify-content-between lh-sm">
            <div>
              <h6 class="my-0">Heinz tomato ketchup</h6>
              <small class="text-body-secondary">Brief description</small>
            </div>
            <span class="text-body-secondary">$5</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Total (USD)</span>
            <strong>$20</strong>
          </li>
        </ul>

        <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
      </div>
    </div>
  </div>

  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasSearch" aria-labelledby="Search">
    <div class="offcanvas-header justify-content-center">
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="order-md-last">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-primary">Search</span>
        </h4>
        <form role="search" action="index.html" method="get" class="d-flex mt-3 gap-0">
          <input class="form-control rounded-start rounded-0 bg-light" type="email" placeholder="What are you looking for?" aria-label="What are you looking for?">
          <button class="btn btn-dark rounded-end rounded-0" type="submit">Search</button>
        </form>
      </div>
    </div>
  </div>

  <header>
    <div class="container" style="max-width: 1600px;">
      <div class="row py-3 border-bottom">

        <div class="col-sm-4 col-lg-3 text-center text-sm-start">
          <div class="main-logo">
            <a href="./index.php">
              <img src="http://localhost/Foodmart/images/logo.png" alt="logo" class="img-fluid">
            </a>
          </div>
        </div>

        <div class="col-sm-12 offset-sm-2 offset-md-0 col-lg-5 d-none d-lg-block">
          <div class="search-bar row bg-light p-2 my-2 rounded-4">
            <div class="col-11 col-md-7">
              <form id="search-form" class="text-center" action="./php-files/search.php" method="get">
                <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Search for more than 20,000 products" />
                <button type="submit" style="display: none;"></button>
              </form>

            </div>
            <div class="col-1 ms-auto">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor" d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="col-sm-8 col-lg-4 d-flex justify-content-end gap-5 align-items-center mt-4 mt-sm-0 justify-content-center justify-content-sm-end">
          <div class="support-box text-end d-none d-xl-block">
            <span class="fs-6 text-muted">For Support?</span>
            <h5 class="mb-0">+91-7698353535</h5>
          </div>

          <ul class="d-flex justify-content-end list-unstyled m-0">
            <li class="dropdown">
              <a href="#" class="rounded-circle bg-light p-2 mx-1 dropdown-toggle text-black" data-bs-toggle="dropdown">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <use xlink:href="#user"></use>
                </svg>
              </a>
              <ul class="dropdown-menu p-0 shadow-lg mt-4" style="border-radius: 10px;">
                <div class="profile-box-1">
                  <p class="text-start" style="font-size: 13px;"><span class="" style="color: #FFC43F; font-size:16px">Welcome to foodmart</span><br>
                    Access account & manage orders</p>
                  <hr class="w-100 me-4 d-block">
                  <a href="./php-files/my-profile.php" class="text-decoration-none mb-2 mt-3 d-block d-link">
                    <li style="font-size: 16px;"><i class="fa-solid fa-user me-3"></i>My Profile</li>
                  </a>
                  <a href="" class="text-decoration-none mb-2 d-block d-link">
                    <li style="font-size: 16px;"><i class="fa-solid fa-bag-shopping me-3"></i>My Orders</li>
                  </a>
                  <a href="" class="text-decoration-none mb-3 d-block d-link">
                    <li style="font-size: 16px;"><i class="fa-solid fa-heart me-3"></i>My wishlist</li>
                  </a>
                  <hr class="w-100 mt-4">

                  <?php if (isset($_SESSION['user_name'])): ?>
                    <!-- <li class="mb-2">Hello, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></li> -->
                    <li><a class="p-2 text-black fw-bold btn btn-warning w-100" href="./php-files/logout.php" style="border: 1px solid #FFC43F;">Logout</a></li>
                  <?php else: ?>
                    <li><a class="p-2 text-black fw-bold btn btn-warning w-100 " href="./php-files/register.php" style="border: 1px solid #FFC43F;">Register</a></li>
                    <li><a class="p-2 text-black fw-bold btn btn-warning w-100 mt-2" href="./php-files/login.php" style="border: 1px solid #FFC43F;">Login</a></li>
                  <?php endif; ?>
                </div>
              </ul>
            </li>
            </li>


            <!--<li>
                                <a href="#" class="rounded-circle bg-light p-2 mx-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <use xlink:href="#heart"></use>
                                    </svg>
                                </a>
                        </li> -->

            <li class="position-relative">
              <a href="./wishlist.php" class="rounded-circle bg-light p-2 mx-1 position-relative text-black">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <use xlink:href="#heart"></use>
                </svg>
                <?php
                include './db.php';

                $wishlist_count = 0;
                if (isset($_SESSION['user_id'])) {
                  $uid = $_SESSION['user_id'];
                  $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM wishlist WHERE user_id = ?");
                  $stmt->bind_param("i", $uid);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  $data = $result->fetch_assoc();
                  $wishlist_count = $data['total'];
                }
                ?>
                <?php if ($wishlist_count > 0): ?>
                  <span class=" badge rounded-pill">
                    <?= $wishlist_count ?>
                  </span>
                <?php endif; ?>
              </a>
            </li>
            <li class="d-lg-none">
              <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <use xlink:href="#cart"></use>
                </svg>
              </a>
            </li>
            <li class="d-lg-none">
              <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSearch" aria-controls="offcanvasSearch">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <use xlink:href="#search"></use>
                </svg>
              </a>
            </li>
          </ul>

          <div class="cart text-end d-none d-lg-block dropdown">
            <button class="border-0 bg-transparent d-flex flex-column gap-2 lh-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
              <span class="fs-6 text-muted dropdown-toggle">Your Cart</span>
              <span class="cart-total fs-5 fw-bold">$1290.00</span>
            </button>
          </div>
        </div>

      </div>
    </div>
    <div class="container" style="max-width: 1600px;">
      <div class="row py-3">
        <div class="d-flex  justify-content-center justify-content-sm-between align-items-center">
          <nav class="main-menu d-flex navbar navbar-expand-lg">

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
              aria-controls="offcanvasNavbar">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">

              <div class="offcanvas-header justify-content-center">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>

              <div class="offcanvas-body">

                <select class="filter-categories border-0 mb-0 me-5" style="background-color:transparent">
                  <option value="">Shop by Categories</option>
                  <?php
                  $conn = new mysqli("localhost", "root", "", "foodmart");
                  $category_query = $conn->query("SELECT id, category_name FROM categories ORDER BY category_name ASC");
                  while ($row = $category_query->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['category_name']) . '</option>';
                  }
                  ?>
                </select>


                <ul class="navbar-nav justify-content-end menu-list list-unstyled d-flex gap-md-3 mb-0">
                  <li class="nav-item active">
                    <a href="#women" class="nav-link">Women</a>
                  </li>
                  <li class="nav-item dropdown">
                    <a href="#men" class="nav-link">Men</a>
                  </li>
                  <li class="nav-item">
                    <a href="#kids" class="nav-link">Kids</a>
                  </li>
                  <li class="nav-item">
                    <a href="#accessories" class="nav-link">Accessories</a>
                  </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" role="button" id="pages" data-bs-toggle="dropdown" aria-expanded="false">Pages</a>
                    <ul class="dropdown-menu" aria-labelledby="pages">
                      <li><a href="index.html" class="dropdown-item">About Us </a></li>
                      <li><a href="index.html" class="dropdown-item">Shop </a></li>
                      <li><a href="index.html" class="dropdown-item">Single Product </a></li>
                      <li><a href="index.html" class="dropdown-item">Cart </a></li>
                      <li><a href="index.html" class="dropdown-item">Checkout </a></li>
                      <li><a href="index.html" class="dropdown-item">Blog </a></li>
                      <li><a href="index.html" class="dropdown-item">Single Post </a></li>
                      <li><a href="index.html" class="dropdown-item">Styles </a></li>
                      <li><a href="index.html" class="dropdown-item">Contact </a></li>
                      <li><a href="index.html" class="dropdown-item">Thank You </a></li>
                      <li><a href="index.html" class="dropdown-item">My Account </a></li>
                      <li><a href="index.html" class="dropdown-item">404 Error </a></li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="#brand" class="nav-link">Brand</a>
                  </li>
                  <li class="nav-item">
                    <a href="#sale" class="nav-link">Sale</a>
                  </li>
                  <li class="nav-item">
                    <a href="#blog" class="nav-link">Blog</a>
                  </li>
                </ul>

              </div>

            </div>
        </div>
      </div>
    </div>
  </header>


  <!-- HTML Content Below: -->

  <div class="container py-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent px-0 mb-4 fs-5">
        <li class="breadcrumb-item"><a href="/Foodmart/index.php" class="text-decoration-none text-warning">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
      </ol>
    </nav>

    <div class="container mt-5">
      <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-md-4">
          <div class="sidebar text-center shadow-sm">
            <img src="../admin/static/uploads/<?= htmlspecialchars($user_image) ?>" alt="User Image" class="profile-img">
            <h5><?= htmlspecialchars($user_name) ?></h5>

            <style>
              .badge-1 {
                position: absolute;
                background-color: #FFC43F;
                left: 91%;
                top: 10%;
                color: white;
                height: 78%;
                width: 7%;
                font-size: 15px;
                padding-top: 5px;
                text-align: center;
              }

              .d-link {
                padding: 8px 10px !important;
              }

              .d-link:hover {
                background-color: #FCF7EB;
                padding: 8px 10px;
                color: #FFC43F;
              }

              .wish-box img {
                width: 100%;
                max-height: 150px;
                object-fit: contain;
              }

              /* Ensuring the trash icon only shows on hover */
              .product-card {
                position: relative;
              }

              .trash-icon {
                display: none;
                /* Hide the icon by default */
              }

              .product-card:hover .trash-icon {
                display: block;
                /* Show the icon on hover */
                cursor: pointer;
              }

              .trash-icon i {
                font-size: 20px;
                /* Adjust the size of the trash icon */
              }
            </style>

            <div class="mt-4 d-grid gap-2 text-start">
              <a href="my-profile.php" class="nav-link d-link"><i class="fa-solid fa-user me-2"></i>My Profile</a>
              <a href="my-orders.php" class="nav-link position-relative p-2 d-link">
                <i class="fa-solid fa-box me-2"></i>My Orders
                <?php
                include './db.php';
                $wishlist_count = 0;
                if (isset($_SESSION['user_id'])) {
                  $uid = $_SESSION['user_id'];
                  $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM wishlist WHERE user_id = ?");
                  $stmt->bind_param("i", $uid);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  $data = $result->fetch_assoc();
                  $wishlist_count = $data['total'];
                }
                ?>
                <?php if ($wishlist_count > 0): ?>
                  <span class="badge-1 rounded"><?= $wishlist_count ?></span>
                <?php endif; ?>
              </a>
              <a href="./wishlist.php" class="nav-link position-relative p-2 d-link">
                <i class="fa-solid fa-heart me-2"></i> Wishlist
                <?php if ($wishlist_count > 0): ?>
                  <span class="badge-1 rounded"><?= $wishlist_count ?></span>
                <?php endif; ?>
              </a>
              <a href="payment-methods.php" class="nav-link d-link"><i class="fa-solid fa-credit-card me-2"></i>Payment Methods</a>
              <a href="account-settings.php" class="nav-link d-link"><i class="fa-solid fa-gear me-2"></i>Account Settings</a>
              <a href="logout.php" class="btn btn-warning mt-3"><i class="fa-solid fa-sign-out-alt me-2"></i>Log Out</a>
            </div>
          </div>
        </div>


        
        <div class="col-md-8">
          <div class="profile-box">
            <h5 class="mb-4">My Wishlist</h5>
            <div class="wishlist-item">
              <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                include './db.php';
                if (isset($_SESSION['user_id'])) {
                  $uid = $_SESSION['user_id'];
                  $stmt = $conn->prepare("
            SELECT p.id, p.product_name AS name, p.product_price AS price, 
                   pi.image_path AS image
            FROM wishlist w
            JOIN products p ON w.product_id = p.id
            LEFT JOIN product_images pi ON p.id = pi.product_id
            WHERE w.user_id = ?
            GROUP BY p.id  -- Ensuring only one image per product
          ");
                  $stmt->bind_param("i", $uid);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                      <div class="col">
                        <div class="card h-100 border shadow-sm position-relative product-card">
                          <div class="card-img-top text-center p-3">
                            <img src="../admin/static/<?= htmlspecialchars($row['image']) ?>"
                              alt="<?= htmlspecialchars($row['name']) ?>"
                              class="img-fluid"
                              style="height: 160px; width: auto; object-fit: contain;">
                            <!-- Trash Icon -->
                            <a href="remove-from-wishlist.php?product_id=<?= $row['id'] ?>"
                              class="position-absolute top-0 end-0 p-2 text-danger trash-icon">
                              <i class="fas fa-trash-alt"></i>
                            </a>
                          </div>
                          <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?= htmlspecialchars($row['name']) ?></h6>
                            <div class="rating text-muted small">★★★★★ (4.5)</div>
                            <div class="price my-2">
                              <span class="text-success fw-bold">$<?= htmlspecialchars($row['price']) ?></span>
                            </div>
                            <form method="POST" action="add-to-cart.php" class="mt-auto">
                              <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                              <a href="../php-files/add_to_cart.php?product_id=<?= $product['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-warning w-100 mt-3">Add to Cart</a>
                            </form>
                          </div>
                        </div>
                      </div>
                <?php
                    endwhile;
                  else:
                    echo '<div class="col-12"><p class="text-muted">Your wishlist is empty.</p></div>';
                  endif;
                }
                ?>
              </div>
            </div>
          </div>
        </div>





      </div>
    </div>
  </div>


  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="../js/jquery-1.11.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  <script src="../js/plugins.js"></script>
  <script src="../js/script.js"></script>
  <script>
    $(document).ready(function() {
      $(document).on('click', '.qty-btn', function() {
        const action = $(this).data('action');
        const productId = $(this).data('product-id');
        const input = $('#qty-' + productId);
        let qty = parseInt(input.val());

        if (action === 'increase') {
          qty += 1;
        } else if (action === 'decrease' && qty > 1) {
          qty -= 1;
        }

        input.val(qty);

        $.ajax({
          url: '../php-files/update_cart.php',
          method: 'POST',
          data: {
            product_id: productId,
            quantity: qty
          },
          dataType: 'json',
          success: function(response) {
            console.log(response);
            if (response.success) {
              $('#item-total-' + productId).text('$' + response.item_total);
              $('#grand-total').text('$' + response.grand_total);
              if (response.subtotal) {
                $('[data-subtotal]').text('$' + response.subtotal);
              }
              if (response.tax) {
                $('[data-tax]').text('$' + response.tax);
              }
            } else {
              alert(response.message || "Failed to update cart. Please try again.");
            }

          },
          error: function(xhr, status, error) {
            try {
              let json = JSON.parse(xhr.responseText);
              alert(json.message || "Something went wrong");
            } catch (e) {
              console.error(xhr.responseText);
              alert("AJAX Error: " + error);
            }
          }

        });

      });
    });
  </script>






</body>

</html>