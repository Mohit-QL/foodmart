<?php
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invalid product ID.');
}



if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
    } else {
        echo "<p>Product not found.</p>";
        exit;
    }
} else {
    echo "<p>Invalid product ID.</p>";
    exit;
}
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>FoodMart</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="./vendor.css">
    <link rel="stylesheet" type="text/css" href="./style.css">
    <style>
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
                            <a href="#" class="rounded-circle bg-light p-2 mx-1 dropdown-toggle" data-bs-toggle="dropdown">
                                <svg width="24" height="24" viewBox="0 0 24 24">
                                    <use xlink:href="#user"></use>
                                </svg>
                            </a>
                            <ul class="dropdown-menu p-3 shadow-sm profile">
                                <?php if (isset($_SESSION['user_name'])): ?>
                                    <li class="mb-2">Hello, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></li>
                                    <li><a class="p-0 text-black fw-bold" href="./php-files/logout.php">Logout</a></li>
                                <?php else: ?>
                                    <li><a class="" href="./php-files/register.php">Register</a></li>
                                    <li><a class="" href="./php-files/login.php">Login</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>


                        <!--<li>
                                <a href="#" class="rounded-circle bg-light p-2 mx-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <use xlink:href="#heart"></use>
                                    </svg>
                                </a>
                        </li> -->

                        <li class="position-relative">
                            <a href="./wishlist.php" class="rounded-circle bg-light p-2 mx-1 position-relative">
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

                                <select class="filter-categories border-0 mb-0 me-5">
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



    <div class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-4 fs-5">
                <li class="breadcrumb-item"><a href="/Foodmart/index.php" class="text-decoration-none text-warning">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Product Details</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-5">
                <div class="zoom-container" style="position: relative; overflow: hidden;">
                    <?php
                    $image_query = $conn->query("SELECT * FROM product_images WHERE product_id = {$product['id']} LIMIT 1");
                    $first_image = $image_query->fetch_assoc();
                    ?>
                    <img id="mainImage" src="http://localhost/foodmart/admin/static/<?= $first_image['image_path'] ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="img-fluid mb-3 main-image" style="height: 400px; object-fit: contain;">
                    <div id="zoomResult" class="zoom-result" style="display: none; position: absolute; top: 0; left: 0; overflow: hidden;"></div>
                </div>

                <div style="margin-top: 15px;">
                    <?php
                    $image_query = $conn->query("SELECT * FROM product_images WHERE product_id = {$product['id']}");
                    while ($image = $image_query->fetch_assoc()):
                    ?>
                        <img src="http://localhost/foodmart/admin/static/<?= $image['image_path'] ?>" class="thumb-img border selected" onclick="document.getElementById('mainImage').src=this.src;">
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="col-md-7">
                <p class="text-uppercase text-muted small"><?= htmlspecialchars($product['category_name']) ?></p>
                <h2><?= htmlspecialchars($product['product_name']) ?></h2>

                <div class="mb-2">
                    <span class="text-success fw-bold fs-4">$<?= number_format($product['product_price'], 2) ?></span>
                    <?php if (!empty($product['old_price'])): ?>
                        <span class="text-muted text-decoration-line-through ms-2">$<?= number_format($product['old_price'], 2) ?></span>
                        <span class="badge bg-danger ms-2">-<?= round(100 - ($product['product_price'] / $product['old_price']) * 100) ?>%</span>
                    <?php endif; ?>
                </div>

                <p><?= htmlspecialchars($product['product_description']) ?></p>
                <p><span class="text-success">● In Stock</span> (24 items left)</p>

                <div class="d-flex gap-2 my-3">
                    <a href="add_to_cart.php?product_id=<?= $product['id'] ?>" class="btn btn-warning w-50"> Add to Cart</a>
                </div>

                <div class="guarantee mt-4">
                    <p><i class="bi bi-truck"></i> Free shipping on orders over $50</p>
                    <p><i class="bi bi-arrow-repeat"></i> 30-day return policy</p>
                    <p><i class="bi bi-shield-check"></i> 2-year warranty</p>
                </div>
            </div>
        </div>
    </div>




    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="js/plugins.js"></script>
    <script src="js/script.js"></script>
    <script>
        function changeQty(val) {
            const input = document.getElementById('qtyInput');
            let current = parseInt(input.value);
            if (!isNaN(current)) {
                current += val;
                if (current < 1) current = 1;
                input.value = current;
            }
        }


        const zoomContainer = document.querySelector('.zoom-container');
        const zoomResult = document.getElementById('zoomResult');

        zoomContainer.addEventListener('mousemove', (e) => {
            const {
                left,
                top,
                width,
                height
            } = mainImage.getBoundingClientRect();
            const x = e.clientX - left;
            const y = e.clientY - top;

            const zoomFactor = 2;
            const zoomWidth = width * zoomFactor;
            const zoomHeight = height * zoomFactor;

            const zoomX = (x / width) * zoomWidth - zoomResult.offsetWidth / 2;
            const zoomY = (y / height) * zoomHeight - zoomResult.offsetHeight / 2;

            zoomResult.style.backgroundImage = `url(${mainImage.src})`;
            zoomResult.style.backgroundSize = `${zoomWidth}px ${zoomHeight}px`;
            zoomResult.style.backgroundPosition = `-${zoomX}px -${zoomY}px`;

            zoomResult.style.display = 'block';

            zoomResult.style.cursor = 'crosshair';
        });

        zoomContainer.addEventListener('mouseleave', () => {
            zoomResult.style.display = 'none';
            zoomResult.style.cursor = 'default';
        });
    </script>
</body>

</html>