<?php
session_start();
header('Content-Type: application/json');

file_put_contents("log.txt", json_encode($_POST) . "\n", FILE_APPEND);
file_put_contents("log.txt", json_encode($_SESSION) . "\n", FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "foodmart");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}

if (!isset($_POST['product_id'], $_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$product_id = (int)$_POST['product_id'];
$quantity = max(1, (int)$_POST['quantity']);
$user_id = (int)$_SESSION['user_id'];

file_put_contents("log.txt", "Product ID: $product_id, User ID: $user_id, Quantity: $quantity\n", FILE_APPEND);

$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param("iii", $quantity, $user_id, $product_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Execution failed: ' . $stmt->error]);
    exit;
}

$stmt = $conn->prepare("SELECT p.product_price FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.product_id = ? AND c.user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

file_put_contents("log.txt", "SQL Query Result: " . json_encode($item) . "\n", FILE_APPEND);

if (!$item) {
    file_put_contents("log.txt", "No matching item found for User ID: $user_id and Product ID: $product_id\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

$item_total = $item['product_price'] * $quantity;

$stmt = $conn->prepare("
    SELECT p.product_price, c.quantity FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subtotal = 0;
while ($row = $result->fetch_assoc()) {
    $subtotal += $row['product_price'] * $row['quantity'];
}

$tax = $subtotal * 0.10;
$total = $subtotal + $tax;

echo json_encode([
    'success' => true,
    'item_total' => number_format($item_total, 2),
    'grand_total' => number_format($total, 2),
    'subtotal' => number_format($subtotal, 2),
    'tax' => number_format($tax, 2)
]);
exit;
