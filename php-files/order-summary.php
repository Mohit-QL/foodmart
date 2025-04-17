<?php
session_start();
include '../php-files/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Summary | FoodMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #FCF7EB;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background-color: #fff;
            border: 1px solid #D8D8D8;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        h3 {
            font-size: 24px;
            color: #A3BE4C;
            margin-bottom: 20px;
            text-align: center;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
            padding: 10px;
            background-color: #E6F3FA;
            border-left: 5px solid #A3BE4C;
            border-radius: 4px;
        }

        .status {
            font-weight: bold;
            color: #fff;
            background-color: #A3BE4C;
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .order-notice {
            text-align: center;
            color: #FFC43F;
            font-weight: bold;
        }

        .btn-home {
            display: inline-block;
            margin-top: 30px;
            background-color: #FFC43F;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .btn-home:hover {
            background-color: #e6a800;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php
        if (isset($_GET['orderID'])) {
            $orderID = $_GET['orderID'];
            $user_id = $_SESSION['user_id'];

            $query = "SELECT * FROM orders WHERE order_id = '$orderID' AND user_id = '$user_id'";
            $result = mysqli_query($conn, $query);
            $order = mysqli_fetch_assoc($result);

            if ($order) {
                echo "<h3>Order Summary</h3>";
                echo "<p><strong>Order ID:</strong> " . $order['order_id'] . "</p>";
                echo "<p><strong>Payer ID:</strong> " . $order['payer_id'] . "</p>";
                echo "<p><strong>Total Amount:</strong> $" . number_format($order['total_amount'], 2) . "</p>";
                echo "<p><strong>Order Date:</strong> " . $order['date'] . "</p>";
                echo "<p><span class='status'>" . $order['status'] . "</span></p>";
                echo "
                <div style='text-align:center; margin-top: 30px;'>
                    <a class='btn-home' href='../index.php' style='margin-right: 15px;'>Back to Home</a>
                    <a class='btn-home' href='cart.php' style='background-color: #A3BE4C;'>Go to Cart</a>
                </div>
            ";
            } else {
                echo "<p class='order-notice'>Order not found.</p>";
            }
        } else {
            echo "<p class='order-notice'>No order found.</p>";
        }
        ?>
    </div>

</body>

</html>