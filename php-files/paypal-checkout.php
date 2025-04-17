<?php
session_start();

$total = 500;
include '../php-files/db.php';
$user_id = $_SESSION['user_id'];
$total = 0;

$cart_query = mysqli_query($conn, "SELECT price, quantity FROM cart WHERE user_id = $user_id");
while ($row = mysqli_fetch_assoc($cart_query)) {
    $total += $row['price'] * $row['quantity'];
}



?>
<!DOCTYPE html>
<html>

<head>
    <title>Pay with PayPal | FoodMart</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AbOOx0G4daPIY1idJP_zr1Ygm_ck2GNW27Zhv3RE10u_OMjBFiloYZhv079uAipJbJ5hF6Fp4wVShzMq&currency=USD"></script>
    <div id="paypal-button-container"></div>
</head>

<body>

    <h3>Total Amount: $<?= number_format($total, 2) ?></h3>


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