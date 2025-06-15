<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit;
}

// Handle verification/confirmation action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $stmt = $dbh->prepare("UPDATE `order` SET order_status = 'Verified' WHERE order_id = ?");
    $stmt->execute([$order_id]);
    header("Location: employee-requests.php");
    exit;
}

// Fetch orders for verification with item details
$sql = "
    SELECT o.order_id, o.order_status, o.order_date, o.total_amount, 
           CONCAT(c.first_name, ' ', c.last_name) AS customer_name
    FROM `order` o
    JOIN customer c ON o.customer_id = c.customer_id
    WHERE o.order_status IN ('Pending', 'Approved', 'Preparing')
    ORDER BY o.order_date DESC
";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$orders = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $order_id = $row['order_id'];
    // Fetch items for each order
    $item_sql = "
        SELECT m.item_name, od.quantity
        FROM order_details od
        JOIN menu m ON od.item_id = m.item_id
        WHERE od.order_id = ?
    ";
    $item_stmt = $dbh->prepare($item_sql);
    $item_stmt->execute([$order_id]);
    $items = [];
    while ($item_row = $item_stmt->fetch(PDO::FETCH_ASSOC)) {
        $items[] = $item_row['item_name'] . " ×" . $item_row['quantity'];
    }
    $row['items'] = implode(", ", $items);
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify Requests</title>
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
      background: rgba(255,255,255,0.2);
      border-left: 4px solid white;
    }
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 2rem;
      flex: 1;
    }
    h1 {
      margin-bottom: 1rem;
      color: var(--text-dark);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    th, td {
      padding: 1rem;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background-color: #f3f3f3;
    }
    .btn-verify {
      background: #007bff;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.95rem;
      transition: background 0.2s;
    }
    .btn-verify:disabled {
      background: #aaa;
      cursor: not-allowed;
    }
    .status {
      font-weight: bold;
      padding: 0.3em 0.7em;
      border-radius: 6px;
      font-size: 0.95em;
      display: inline-block;
    }
    .status-pending { color: #ffc107; }
    .status-approved { color: #17a2b8; }
    .status-preparing { color: #6f42c1; }
    .status-verified { color: #28a745; }
    @media (max-width: 900px) {
      .main-content { padding: 1rem; }
      .sidebar { width: 100px; }
      .sidebar h2 { font-size: 1rem; }
      .sidebar ul li a { padding: 0.5rem 0.5rem; font-size: 0.9rem; }
      .main-content { margin-left: 100px; }
    }
    @media (max-width: 600px) {
      .sidebar { display: none; }
      .main-content { margin-left: 0; }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>FastBite</h2>
    <ul>
<li><a href="employee-dashboard.php">Home</a></li>
      <li><a href="process-order.php">Process Order</a></li>
      <li><a href="employee-requests.php" class="active">Requests</a></li>
      <li><a href="manage-menu.php">Manage Menu</a></li>
      <li><a href="logout.php">Log out</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Order Requests for Verification</h1>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Date</th>
          <th>Items</th>
          <th>Total</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($orders): ?>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><?= htmlspecialchars($order['order_id']) ?></td>
              <td><?= htmlspecialchars($order['customer_name']) ?></td>
              <td><?= htmlspecialchars($order['order_date']) ?></td>
              <td><?= htmlspecialchars($order['items']) ?></td>
              <td>₱<?= number_format($order['total_amount'], 2) ?></td>
              <td>
                <span class="status status-<?= strtolower($order['order_status']) ?>">
                  <?= htmlspecialchars($order['order_status']) ?>
                </span>
              </td>
              <td>
                <?php if ($order['order_status'] !== 'Verified'): ?>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <button class="btn-verify" type="submit">Verify & Confirm</button>
                  </form>
                <?php else: ?>
                  <button class="btn-verify" disabled>Verified</button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7">No requests found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>