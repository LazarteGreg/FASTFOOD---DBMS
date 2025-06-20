<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('includes/config.php');

header('Content-Type: application/json');

// Debug: confirm file is called
file_put_contents('debug_order.log', "process-payment.php called\n", FILE_APPEND);

try {
    $data = json_decode(file_get_contents('php://input'), true);
    // Debug: log payload
    file_put_contents('debug_order.log', "Payload: " . json_encode($data) . "\n", FILE_APPEND);

    if (!isset($_SESSION['user_id']) || empty($data['items'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid session or empty cart']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $payment_method = $data['payment_method'];
    $discount = $data['discount'];
    $total_amount = $data['total_amount'];
    $date = date('Y-m-d H:i:s');

    // Fetch the correct customer_id using user_id from session
    $customer_id = null;
    $stmt_cust = $dbh->prepare("SELECT customer_id FROM customer WHERE user_id = ?");
    $stmt_cust->execute([$user_id]);
    $customer_id = $stmt_cust->fetchColumn();
    if (!$customer_id) {
        echo json_encode(['status' => 'error', 'message' => 'Customer profile not found.']);
        exit;
    }

    // Insert order (with status and null employee_id)
    $stmt = $dbh->prepare("INSERT INTO `order` (customer_id, total_amount, order_date, order_status, employee_id) VALUES (?, ?, ?, ?, NULL)");
    $stmt->execute([$customer_id, $total_amount, $date, 'pending']);
    $order_id = $dbh->lastInsertId();

    // Insert payment info (with all required fields)
    $payment_status = 'Paid';
    $payment_date = date('Y-m-d H:i:s');
    $amount_paid = $total_amount; // or adjust if you have discounts applied
    $stmt_payment = $dbh->prepare("INSERT INTO payment (order_id, payment_method, payment_status, payment_date, discount, amount_paid) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_payment->execute([$order_id, $payment_method, $payment_status, $payment_date, $discount, $amount_paid]);

    // Optionally, insert into receipt table
    $payment_id = $dbh->lastInsertId();
    $receipt_date = date('Y-m-d H:i:s');
    $stmt_receipt = $dbh->prepare("INSERT INTO receipt (payment_id, receipt_date) VALUES (?, ?)");
    $stmt_receipt->execute([$payment_id, $receipt_date]);

    // Insert order items
    $stmt_item = $dbh->prepare("INSERT INTO order_details (order_id, item_id, quantity) VALUES (?, ?, ?)");
    foreach ($data['items'] as $item) {
        $stmt_item->execute([$order_id, $item['ItemID'], $item['ItemQuantity']]);
    }

    // Clear cart
    unset($_SESSION['cart']);

    echo json_encode([
        'status' => 'success',
        'order_id' => $order_id,
        'customer_id' => $user_id,
        'date' => $date
    ]);
} catch (Exception $e) {
    file_put_contents('debug_order.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
