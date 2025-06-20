<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit;
}

// === Handle AJAX requests ===
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    // Fetch orders with customer and item details
    $sql = "
        SELECT o.order_id, o.total_amount, o.order_status, o.order_date,
               CONCAT(c.first_name, ' ', c.last_name) AS customer_name
        FROM `order` o
        JOIN customer c ON o.customer_id = c.customer_id
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

        $orders[] = [
            'order_id' => $order_id,
            'customer_name' => $row['customer_name'],
            'items' => implode(", ", $items),
            'total_amount' => $row['total_amount'],
            'status' => $row['order_status']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($orders);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    // Update order status and set employee_id only if it is currently NULL
    $employee_id = $_SESSION['user_id'];
    $update_sql = "UPDATE `order` SET order_status = ?, employee_id = IF(employee_id IS NULL, ?, employee_id) WHERE order_id = ?";
    $update_stmt = $dbh->prepare($update_sql);
    $success = $update_stmt->execute([$status, $employee_id, $order_id]);

    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Process Orders</title>
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
    .status-select {
      padding: 0.4rem;
      border-radius: 6px;
      font-size: 0.9rem;
    }
    .status-preparing { color: orange; }
    .status-ready { color: green; }
    .status-picked { color: blue; }
    .status-ontheway { color: darkblue; }
    .status-delivered { color: #28a745; }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>FastBite</h2>
    <ul>
      <li><a href="employee-dashboard.php">Home</a></li>
      <li><a href="process-order.php" class="active">Process Order</a></li>
      <li><a href="employee-requests.php">Requests</a></li>
      <li><a href="manage-menu.php">Manage Menu</a></li>
      <li><a href="logout.php">Log out</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Process Customer Orders</h1>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer Name</th>
          <th>Items</th>
          <th>Total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="order-table-body">
        <tr><td colspan="5">Loading orders...</td></tr>
      </tbody>
    </table>
  </div>

  <script>
    function updateStatusColor(select) {
      select.className = "status-select";
      const value = select.value;

      if (value === "Preparing") select.classList.add("status-preparing");
      else if (value === "Ready for Pick-up") select.classList.add("status-ready");
      else if (value === "Picked Up by Rider") select.classList.add("status-picked");
      else if (value === "On the Way") select.classList.add("status-ontheway");
      else if (value === "Delivered") select.classList.add("status-delivered");
    }

    function fetchOrders() {
      fetch('process-order.php?action=fetch')
        .then(response => response.json())
        .then(orders => {
          const tbody = document.getElementById('order-table-body');
          tbody.innerHTML = '';

          orders.forEach(order => {
            const row = document.createElement('tr');

            row.innerHTML = `
              <td>${order.order_id}</td>
              <td>${order.customer_name}</td>
              <td>${order.items}</td>
              <td>₱${parseFloat(order.total_amount).toFixed(2)}</td>
              <td>
                <select class="status-select" onchange="updateOrderStatus(this, ${order.order_id})">
                  <option ${order.status === 'Preparing' ? 'selected' : ''}>Preparing</option>
                  <option ${order.status === 'Ready for Pick-up' ? 'selected' : ''}>Ready for Pick-up</option>
                  <option ${order.status === 'Picked Up by Rider' ? 'selected' : ''}>Picked Up by Rider</option>
                  <option ${order.status === 'On the Way' ? 'selected' : ''}>On the Way</option>
                  <option ${order.status === 'Delivered' ? 'selected' : ''}>Delivered</option>
                </select>
              </td>
            `;
            tbody.appendChild(row);
            updateStatusColor(row.querySelector('select'));
          });
        });
    }

    function updateOrderStatus(select, orderId) {
      const newStatus = select.value;
      updateStatusColor(select);

      fetch('process-order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${orderId}&status=${encodeURIComponent(newStatus)}`
      }).then(res => res.json()).then(res => {
        if (!res.success) alert("Failed to update status.");
      });
    }

    fetchOrders();
  </script>
</body>
</html>
