<?php
session_start();
include '../php-files/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$total = 0;

// Calculate total from cart
$cart_query = mysqli_query($conn, "SELECT price, quantity FROM cart WHERE user_id = $user_id");
while ($row = mysqli_fetch_assoc($cart_query)) {
    $total += $row['price'] * $row['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pay with PayPal | FoodMart</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AbOOx0G4daPIY1idJP_zr1Ygm_ck2GNW27Zhv3RE10u_OMjBFiloYZhv079uAipJbJ5hF6Fp4wVShzMq&currency=USD"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #FCF7EB;
            color: #333;
        }

        .container {
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        h3 {
            color: #A3BE4C;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .amount {
            font-size: 20px;
            margin-bottom: 30px;
            background-color: #E6F3FA;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #A3BE4C;
        }

        #paypal-button-container {
            margin-top: 20px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 20px;
            background-color: #FFC43F;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background-color: #e6a800;
        }
    </style>
</head>

<body>

    <div class="container">
        <h3>Pay with PayPal</h3>
        <div class="amount">Total Amount: $<?= number_format($total, 2) ?></div>

        <div id="paypal-button-container"></div>

        <a class="back-btn" href="cart.php">Back to Cart</a>
    </div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= number_format($total, 2, '.', '') ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    window.location.href = 'paypal-success.php?orderID=' + data.orderID + '&payerID=' + data.payerID;
                });
            }
        }).render('#paypal-button-container');
    </script>

</body>

</html>
