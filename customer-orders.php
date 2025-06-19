<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

// Get customer's orders
try {
    // Get all orders for the customer with customer details
    $sql = "SELECT o.order_id, o.order_date, o.total_amount, o.order_status as status,
                   c.first_name, c.last_name, c.email, c.phone_number,
                   c.street, c.city, c.postal_code,
                   p.payment_status, p.payment_method, p.discount
            FROM `order` o
            JOIN customer c ON o.customer_id = c.customer_id
            LEFT JOIN payment p ON o.order_id = p.order_id
            WHERE o.customer_id = ?
            ORDER BY o.order_id DESC";
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter orders into ongoing and history
    $ongoing_orders = array_filter($orders, function($order) {
        return in_array(strtolower($order['status']), ['pending', 'preparing', 'ready', 'out_for_delivery']);
    });
    
    $history_orders = array_filter($orders, function($order) {
        return in_array(strtolower($order['status']), ['completed', 'cancelled']);
    });

} catch (PDOException $e) {
    $error = "Error loading orders: " . $e->getMessage();
}

// Handle order cancellation (same as in track-order.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_order_id = intval($_POST['cancel_order_id']);
    $check = $dbh->prepare("SELECT * FROM `order` WHERE order_id = ? AND customer_id = ? AND order_status = 'pending'");
    $check->execute([$cancel_order_id, $_SESSION['user_id']]);
    $order = $check->fetch(PDO::FETCH_ASSOC);
    if ($order) {
        $insert = $dbh->prepare("INSERT INTO order_cancellation (order_id, cancellation_reason, cancellation_date) VALUES (?, ?, NOW())");
        $insert->execute([$cancel_order_id, 'User cancelled']);
        $update = $dbh->prepare("UPDATE `order` SET order_status = 'cancelled' WHERE order_id = ?");
        $update->execute([$cancel_order_id]);
        header("Location: customer-orders.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
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
            margin: 0;
            padding: 0.5rem 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            display: block;
            transition: all 0.3s ease;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.2);
            padding-left: 2rem;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid white;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            flex: 1;
            min-height: 100vh;
            background-color: var(--bg-light);
            position: relative;
        }
        .main-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            min-height: 100vh;
            width: calc(100vw - var(--sidebar-width));
            box-sizing: border-box;
        }
        @media (max-width: 900px) {
            .main-content {
                margin-left: 0;
                width: 100vw;
                border-radius: 0;
                padding: 1rem;
            }
            .sidebar {
                position: static;
                width: 100vw;
                border-radius: 0;
                padding-top: 1rem;
            }
        }
        .orders-section {
            margin-bottom: 2rem;
        }
        .orders-section h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            width: 100%;
        }
        .order-item {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
        }
        .order-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .order-item-header h3 {
            color: var(--text-dark);
            margin: 0;
            font-size: 1.1rem;
        }
        
        .order-item-body {
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-meta {
            display: flex;
            gap: 2rem;
        }
        
        .order-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .order-status {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: capitalize;
            margin: 0;
        }
        
        .order-meta p {
            margin: 0.3rem 0;
            color: #555;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .order-meta strong {
            color: var(--text-dark);
            min-width: 60px;
            display: inline-block;
        }
        
        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-pending { color: #ffc107; }
        .status-preparing { color: #28a745; }
        .status-ready { color: #17a2b8; }
        .status-out_for_delivery { color: #6f42c1; }
        .status-completed { color: #28a745; }
        .status-cancelled { color: #dc3545; }
        .actions {
            text-align: right;
        }
        .view-receipt-btn {
            background-color: var(--primary);
            color: white;
            padding: 0.6rem 1.25rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        
        .view-receipt-btn:hover {
            background-color: #b34040;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .view-receipt-btn:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .order-details {
            margin-top: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .info {
            margin-bottom: 1.5rem;
        }
        .info p {
            margin: 0.3rem 0;
        }
        .order-items-container {
            margin-top: 1.5rem;
        }
        
        .order-items-container h4 {
            color: var(--primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .order-items {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .order-items td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
            vertical-align: top;
        }
        
        .order-items tbody tr:last-child td {
            border-bottom: none;
        }
        
        .order-items tfoot td {
            background: #f8f9fa;
            font-weight: 600;
            padding: 1rem;
        }
        
        .text-right {
            text-align: right;
        }
        
        .order-total {
            font-size: 1.1em;
            color: var(--primary);
        }
        
        .order-items th {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .no-orders {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        @media (max-width: 900px) {
            .main-content { padding: 1rem; }
            .sidebar { width: 100px; }
            .sidebar h2 { font-size: 1rem; }
            .sidebar ul li a { padding: 0.5rem 0.5rem; font-size: 0.9rem; }
            .main-content { margin-left: 100px; }
            .order-item {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 600px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
    <style>
        .confirmation-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .confirmation-dialog {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
        }

        .confirmation-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .confirmation-button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .confirmation-button.confirm {
            background-color: var(--primary);
            color: white;
        }

        .confirmation-button.cancel {
            background-color: #ccc;
            color: #333;
        }

        .confirmation-button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <h2>FastBite</h2>
        <ul>
            <li><a href="customer-dashboard.php">Home</a></li>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="customer-orders.php" class="active">My Orders</a></li>
            <li><a href="logout.php">Log out</a></li>
        </ul>
    </nav>
    
    <div class="main-content">
        <div class="orders-container">
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($ongoing_orders)): ?>
            <div class="orders-section">
                <h2>Ongoing Orders</h2>
                <div class="orders-list">
                    <?php foreach ($ongoing_orders as $order): ?>
                    <div class="order-item">
                        <div class="order-item-header">
                            <h3>Order #<?= htmlspecialchars($order['order_id']) ?></h3>
                        </div>
                        <div class="order-item-body">
                            <div class="order-meta">
                                <p><strong>Date:</strong> <?= date('F j, Y', strtotime($order['order_date'])) ?></p>
                                <p><strong>Total:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>
                            </div>
                            <div class="order-actions">
                                <span class="order-status status-<?= strtolower(str_replace(' ', '_', $order['status'])) ?>">
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['status']))) ?>
                                </span>
                                <?php if (strtolower($order['status']) === 'pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="cancel_order_id" value="<?= $order['order_id'] ?>">
                                    <button class="view-receipt-btn" type="submit" onclick="return confirm('Cancel this order?')">Cancel</button>
                                </form>
                                <?php endif; ?>
                                <?php if (strtolower($order['status']) !== 'cancelled'): ?>
                                <button class="view-receipt-btn" onclick="toggleDetails('<?= $order['order_id'] ?>')">View Details</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div id="order-details-<?= $order['order_id'] ?>" class="order-details" style="display: none;">
                            <div class="customer-info">
                                <h4>Customer Information</h4> <br>
                                <p><strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone_number']) ?></p>
                                <p><strong>Address:</strong> <?= htmlspecialchars($order['street'] . ', ' . $order['city'] . ' ' . $order['postal_code']) ?></p> <br>
                            </div>
                            <table class="order-items">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $itemsQuery = $dbh->prepare("
                                        SELECT m.item_name, od.quantity, m.price, (m.price * od.quantity) as subtotal
                                        FROM order_details od
                                        JOIN menu m ON od.item_id = m.item_id
                                        WHERE od.order_id = ?
                                    ");
                                    $itemsQuery->execute([$order['order_id']]);
                                    $items = $itemsQuery->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>₱<?= number_format($item['subtotal'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="text-align: right; font-weight: bold;">Total Paid:</td>
                                        <td style="text-align: right; font-weight: bold;">₱<?= number_format($order['total_amount'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="text-align: right;">
                                            <button onclick="window.location.href='menu.php'" style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem; margin-top: 1.5rem;">Back to Menu</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($orders)): ?>
            <div class="section">
              <h2 style="font-size:2rem; color:#cc5050; margin-bottom:1.5rem; letter-spacing:1px;">Order History</h2>
              <table id="past-orders" style="width:100%; border-collapse:collapse; background:white; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-radius:10px; overflow:hidden;">
                <thead>
                  <tr style="background:#f1f1f1;">
                    <th style="padding:1rem 0.5rem; font-weight:600; color:#333;">Order ID</th>
                    <th style="padding:1rem 0.5rem; font-weight:600; color:#333;">Date</th>
                    <th style="padding:1rem 0.5rem; font-weight:600; color:#333;">Items</th>
                    <th style="padding:1rem 0.5rem; font-weight:600; color:#333;">Total</th>
                    <th style="padding:1rem 0.5rem; font-weight:600; color:#333;">Status</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                  <?php if ($order['status'] === 'completed' || $order['status'] === 'cancelled'): ?>
                    <tr style="border-bottom:1px solid #eee;">
                      <td style="padding:0.9rem 0.5rem; font-size:1.1rem; color:#222;"><?= $order['id'] ?? $order['order_id'] ?></td>
                      <td style="padding:0.9rem 0.5rem; color:#555; white-space:nowrap;"><?= $order['date'] ?? $order['order_date'] ?></td>
                      <td style="padding:0.9rem 0.5rem; color:#444;">
                        <?php
                        if (isset($order['items'])) {
                          echo implode(', ', array_map(function($i){return $i['name'].' ×'.$i['quantity'];}, $order['items']));
                        } else {
                          $itemsQuery = $dbh->prepare("SELECT m.item_name, od.quantity FROM order_details od JOIN menu m ON od.item_id = m.item_id WHERE od.order_id = ?");
                          $itemsQuery->execute([$order['order_id']]);
                          $items = $itemsQuery->fetchAll(PDO::FETCH_ASSOC);
                          $itemStrings = array_map(function($i){return $i['item_name'].' ×'.$i['quantity'];}, $items);
                          echo implode(', ', $itemStrings);
                        }
                        ?>
                      </td>
                      <td style="padding:0.9rem 0.5rem; color:#cc5050; font-weight:600;">₱<?= isset($order['total']) ? number_format($order['total'], 2) : number_format($order['total_amount'], 2) ?></td>
                      <td style="padding:0.9rem 0.5rem;">
                        <span class="status <?= $order['status'] ?>" style="padding:5px 16px; border-radius:16px; font-size:1rem; text-transform:capitalize; background:<?= $order['status']==='cancelled' ? '#fddede' : '#c9f7c9' ?>; color:<?= $order['status']==='cancelled' ? '#912d2d' : '#256029' ?>;">
                          <?= $order['status'] ?>
                        </span>
                      </td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php endif; ?>

            <?php if (empty($ongoing_orders) && empty($orders)): ?>
            <div class="no-orders">
                <p>No orders found.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

<script>
    function toggleDetails(orderId) {
        const detailsDiv = document.getElementById('order-details-' + orderId);
        const button = document.querySelector(`button[onclick^="toggleDetails('${orderId}')"]`);
        
        if (detailsDiv) {
            if (detailsDiv.style.display === 'none' || !detailsDiv.style.display) {
                detailsDiv.style.display = 'block';
                if (button) button.textContent = 'Hide Details';
            } else {
                detailsDiv.style.display = 'none';
                if (button) button.textContent = 'View Details';
            }
        }
    }

    // Close order details when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.order-item')) {
            document.querySelectorAll('.order-details').forEach(details => {
                details.style.display = 'none';
                const orderId = details.id.replace('order-details-', '');
                const button = document.querySelector(`button[onclick^="toggleDetails('${orderId}')"]`);
                if (button) button.textContent = 'View Details';
            });
        }
    });

    // Prevent event propagation when clicking inside order items
    document.querySelectorAll('.order-item').forEach(item => {
        item.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });

    // Close details when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.order-item')) {
            document.querySelectorAll('.order-details').forEach(details => {
                details.style.display = 'none';
                const orderId = details.id.replace('order-details-', '');
                const button = document.querySelector(`button[onclick^="toggleDetails('${orderId}')"]`);
                if (button) button.textContent = 'View Details';
            });
        }
    });
</script>
</body>
</html>
