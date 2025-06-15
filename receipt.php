<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header('Location: cart.php');
    exit;
}

// Fetch order details
$stmt = $dbh->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<h2>Order not found.</h2>";
    exit;
}

// Only show receipt if payment is confirmed
if (strtolower($order['status']) !== 'paid') {
    echo "<h2>Receipt is available after payment confirmation.</h2>";
    echo '<a href="payment.php?order_id=' . urlencode($order_id) . '">Go to Payment</a>';
    exit;
}

// Fetch order items
$stmt = $dbh->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - FastBite</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 2rem; }
        h1 { color: #cc5050; text-align: center; }
        .info { margin-bottom: 1.5rem; }
        .info p { margin: 0.3rem 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        th, td { padding: 0.7rem; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f5f5f5; }
        .total { text-align: right; font-size: 1.2rem; font-weight: bold; }
        .back-btn { display: block; margin: 1rem auto 0; background: #cc5050; color: #fff; border: none; padding: 0.7rem 1.5rem; border-radius: 5px; cursor: pointer; text-align: center; text-decoration: none; }
        .back-btn:hover { background: #b84444; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Receipt</h1>
        <div class="info">
            <p><strong>Receipt ID:</strong> R<?= htmlspecialchars($order['order_id']) ?></p>
            <p><strong>Customer ID:</strong> <?= htmlspecialchars($order['customer_id']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
            <p><strong>Discount:</strong> <?= htmlspecialchars($order['discount']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; foreach ($items as $item): 
                    $subtotal = $item['item_price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td>₱<?= number_format($item['item_price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">Total Paid: ₱<?= number_format($order['total_amount'], 2) ?></div>
        <a href="menu.php" class="back-btn">Back to Menu</a>
    </div>
</body>
</html>