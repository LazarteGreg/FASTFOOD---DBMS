<?php
session_start();
include('includes/config.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id']) || empty($data['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session or empty cart']);
    exit;
}

$user_id = $_SESSION['user_id'];
$payment_method = $data['payment_method'];
$discount = $data['discount'];
$total_amount = $data['total_amount'];
$date = date('Y-m-d H:i:s');

// Insert order
$stmt = $dbh->prepare("INSERT INTO orders (customer_id, total_amount, payment_method, discount, order_date, status) VALUES (?, ?, ?, ?, ?, 'Paid')");
$stmt->execute([$user_id, $total_amount, $payment_method, $discount, $date]);
$order_id = $dbh->lastInsertId();

// Insert order items
$stmt_item = $dbh->prepare("INSERT INTO order_items (order_id, item_name, item_price, quantity) VALUES (?, ?, ?, ?)");
foreach ($data['items'] as $item) {
    $stmt_item->execute([$order_id, $item['ItemName'], $item['ItemPrice'], $item['ItemQuantity']]);
}

// Clear cart
unset($_SESSION['cart']);

echo json_encode([
    'status' => 'success',
    'order_id' => $order_id,
    'customer_id' => $user_id,
    'date' => $date
]);
