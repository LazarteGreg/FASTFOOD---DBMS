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

// Fetch order details with payment info and customer address
$stmt = $dbh->prepare("
    SELECT o.*, p.payment_status, p.payment_method, p.discount, c.street, c.city, c.postal_code
    FROM `order` o
    LEFT JOIN payment p ON o.order_id = p.order_id
    LEFT JOIN customer c ON o.customer_id = c.customer_id
    WHERE o.order_id = ? AND o.customer_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<h2>Order not found.</h2>";
    exit;
}

// Only show receipt if payment is confirmed
if (strtolower($order['payment_status']) !== 'paid') {
    echo "<h2>Receipt is available after payment confirmation.</h2>";
    echo '<a href="payment.php?order_id=' . urlencode($order_id) . '">Go to Payment</a>';
    exit;
}

// Fetch order items with menu info
$stmt = $dbh->prepare("
    SELECT od.*, m.item_name, m.price
    FROM order_details od
    JOIN menu m ON od.item_id = m.item_id
    WHERE od.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - FastBite</title>
    <style>
        :root {
      --sidebar-width: 220px;
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

    .sidebar {
      width: var(--sidebar-width);
      background: linear-gradient(to right, var(--primary), var(--secondary));
      color: white;
      padding-top: 2rem;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1.5rem;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      margin: 1rem 0;
    }

    .sidebar ul li a {
      color: white;
      text-decoration: none;
      padding: 0.8rem 1.5rem;
      display: block;
      transition: background 0.3s;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
      background: rgba(255, 255, 255, 0.2);
      border-left: 4px solid white;
    }

    .main-content {
      margin-left: var(--sidebar-width);
      padding: 2rem;
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .main-content h1 {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: var(--text-dark);
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
    .print-btn { background-color: var(--primary); color: white; padding: 10px 16px; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; margin-bottom: 1rem; }
    .print-btn:hover { background-color: #b84343; }
    .status-instock { color: #28a745; font-weight: bold; }
    .status-lowstock { color: #ffc107; font-weight: bold; }
    .status-outstock { color: #dc3545; font-weight: bold; }
    .status-unknown { color: #6c757d; font-weight: bold; }
    @media print { .sidebar, .print-btn { display: none !important; } .main-content { margin: 0; padding: 0; } }
    </style>
</head>
<body>
    <nav class="sidebar">
        <h2>FastBite</h2>
        <ul>
            <li><a href="customer-dashboard.php">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="track-order.php">Orders</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Log out</a></li> 
        </ul>
    </nav>
    <div class="container" style="margin-left: auto; height: fit-content;">
        <h1>Receipt</h1> <br>
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
                <?php $total = 0; foreach ($items as $item): 
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
        <br> <br>
        <button class="print-btn" onclick="window.print()" style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem; min-width: 200px; max-width: 200px; width: 200px;"><i class="fa fa-print"></i> Print Receipt</button>
        <br> <br> <br>
        <button onclick="window.location.href='menu.php'" style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem; min-width: 200px; max-width: 200px; width: 200px;">Back to Menu</button>
            
</body>
</html>