<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// --- HANDLE APPROVE/REJECT ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $id = $_POST['id'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($type === 'order' && $id) {
        // Approve or reject new order
        if ($action === 'approve') {
            $stmt = $dbh->prepare("UPDATE `order` SET order_status = 'Approved' WHERE order_id = ?");
            $stmt->execute([$id]);
        } elseif ($action === 'reject') {
            $stmt = $dbh->prepare("UPDATE `order` SET order_status = 'Rejected' WHERE order_id = ?");
            $stmt->execute([$id]);
        }
    } elseif ($type === 'cancel' && $id) {
        // Approve or reject cancellation request
        $stmt = $dbh->prepare("SELECT order_id FROM order_cancellation WHERE cancellation_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $order_id = $row['order_id'];
            if ($action === 'approve') {
                $stmt2 = $dbh->prepare("UPDATE `order` SET order_status = 'Cancelled' WHERE order_id = ?");
                $stmt2->execute([$order_id]);
            } elseif ($action === 'reject') {
                $stmt2 = $dbh->prepare("DELETE FROM order_cancellation WHERE cancellation_id = ?");
                $stmt2->execute([$id]);
            }
        }
    }
    // After handling, reload the page to update the tables
    header("Location: requests.php");
    exit;
}

$orderRequests = [];
$cancelRequests = [];

// Fetch new order requests (Pending orders)
$orderQuery = "
    SELECT 
        o.order_id, 
        u.username AS customer, 
        o.total_amount
    FROM `order` o
    JOIN users u ON o.customer_id = u.user_id
    WHERE o.order_status = 'Pending'
    ORDER BY o.order_id DESC
";
$orderStmt = $dbh->prepare($orderQuery);
$orderStmt->execute();
$orderRequests = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch cancellation requests
$cancelQuery = "
    SELECT 
        c.cancellation_id, 
        c.order_id,
        u.username AS customer, 
        c.cancellation_reason, 
        o.total_amount
    FROM order_cancellation c
    JOIN `order` o ON c.order_id = o.order_id
    JOIN users u ON o.customer_id = u.user_id
    ORDER BY c.cancellation_id DESC
";
$cancelStmt = $dbh->prepare($cancelQuery);
$cancelStmt->execute();
$cancelRequests = $cancelStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order Requests - FastBite</title>
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
    }

    .main-content h1 {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: var(--text-dark);
    }

    .section-title {
      font-size: 1.3rem;
      color: var(--primary);
      margin: 2rem 0 1rem;
      border-bottom: 2px solid #eee;
      padding-bottom: 0.5rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      margin-bottom: 2rem;
    }

    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background: #f3f3f3;
    }

    .btn {
      padding: 0.4rem 0.8rem;
      margin-right: 0.5rem;
      font-size: 0.9rem;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .approve-btn {
      background-color: #28a745;
      color: white;
    }

    .reject-btn {
      background-color: #dc3545;
      color: white;
    }
  </style>
</head>
<body>
<nav class="sidebar">
  <h2>FastBite</h2>
  <ul>
    <li><a href="admin-dashboard.php">Home</a></li>
    <li><a href="employee-database.php">Employees</a></li>
    <li><a href="requests.php" class="active">Requests</a></li>
    <li><a href="sales-report.php">Sales</a></li>
    <li><a href="inventory-report.php">Inventory</a></li>
    <li><a href="logout.php">Log out</a></li>
  </ul>
</nav>

<div class="main-content">
  <h1>Requests</h1>

  <div class="section-title">New Order Requests</div>
  <table>
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orderRequests as $order): ?>
      <tr>
        <td><?= htmlspecialchars($order['order_id']) ?></td>
        <td><?= htmlspecialchars($order['customer']) ?></td>
        <td>₱<?= number_format($order['total_amount'], 2) ?></td>
        <td>
          <form method="POST" action="requests.php" style="display:inline">
            <input type="hidden" name="type" value="order">
            <input type="hidden" name="id" value="<?= $order['order_id'] ?>">
            <input type="hidden" name="action" value="approve">
            <button class="btn approve-btn" type="submit">Approve</button>
          </form>
          <form method="POST" action="requests.php" style="display:inline">
            <input type="hidden" name="type" value="order">
            <input type="hidden" name="id" value="<?= $order['order_id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button class="btn reject-btn" type="submit">Reject</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="section-title">Order Cancellation Requests</div>
  <table>
    <thead>
      <tr>
        <th>Cancellation ID</th>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Reason</th>
        <th>Total</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cancelRequests as $cancel): ?>
      <tr>
        <td><?= htmlspecialchars($cancel['cancellation_id']) ?></td>
        <td><?= htmlspecialchars($cancel['order_id']) ?></td>
        <td><?= htmlspecialchars($cancel['customer']) ?></td>
        <td><?= htmlspecialchars($cancel['cancellation_reason']) ?></td>
        <td>₱<?= number_format($cancel['total_amount'], 2) ?></td>
        <td>
          <form method="POST" action="requests.php" style="display:inline">
            <input type="hidden" name="type" value="cancel">
            <input type="hidden" name="id" value="<?= $cancel['cancellation_id'] ?>">
            <input type="hidden" name="action" value="approve">
            <button class="btn approve-btn" type="submit">Approve</button>
          </form>
          <form method="POST" action="requests.php" style="display:inline">
            <input type="hidden" name="type" value="cancel">
            <input type="hidden" name="id" value="<?= $cancel['cancellation_id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button class="btn reject-btn" type="submit">Reject</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>