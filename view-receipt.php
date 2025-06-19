<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? null;
$customer_id = $_GET['customer_id'] ?? null;

// Verify customer_id matches session
if (!$order_id || !$customer_id || $customer_id != $_SESSION['user_id']) {
    header('Location: customer-orders.php');
    exit;
}

// Get order details
try {
    $sql = "SELECT o.order_id, o.order_date, o.total_amount, o.order_status as status, 
                   c.first_name, c.last_name, c.email, c.phone_number, 
                   c.street, c.city, c.postal_code,
                   p.payment_method, p.discount, p.payment_status
            FROM `order` o
            JOIN customer c ON o.customer_id = c.customer_id
            JOIN payment p ON o.order_id = p.order_id
            WHERE o.order_id = ? AND o.customer_id = ?";
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$order_id, $customer_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<h2>Order not found.</h2>";
        exit;
    }

    // Get order items
    $sql = "SELECT i.item_name, oi.quantity, i.price
            FROM `order_item` oi
            JOIN items i ON oi.item_id = i.item_id
            WHERE oi.order_id = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<h2>Error loading order details: " . $e->getMessage() . "</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Receipt</title>
    <style>
        :root {
            --primary: #cc5050;
            --secondary: #d3c260;
            --bg-light: #fff8f0;
            --text-dark: #333;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", sans-serif;
        }

        body {
            display: flex;
            background-color: var(--bg-light);
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 { color: #cc5050; text-align: center; }
        .info { margin-bottom: 1.5rem; }
        .info p { margin: 0.3rem 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f3f3f3; }
        .status-badge {
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #cc5050;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Receipt</h1>
    <div class="info">
        <p><strong>Receipt ID:</strong> R<?= htmlspecialchars($order['order_id']) ?></p>
        <p><strong>Customer ID:</strong> <?= htmlspecialchars($order['customer_id']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
        <p><strong>Discount:</strong> <?= htmlspecialchars($order['discount']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['street'] . ', ' . $order['city'] . ', ' . $order['postal_code']) ?></p>
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
            <?php $total = 0; foreach ($order_items as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td>₱<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₱<?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="total">Total Paid: ₱<?= number_format($order['total_amount'], 2) ?></div>
    
    <a href="menu.php" class="back-link">Back to Menu</a>
</body>
</html>
