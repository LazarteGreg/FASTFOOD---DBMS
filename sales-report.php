<?php
session_start();
include('includes/config.php'); // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch sales data including Net_Amount
$sql = "SELECT Sale_Date, Sales_ID, Order_ID, Discount, Total_Amount, 
        (Total_Amount - (Total_Amount * Discount / 100)) AS Net_Amount
        FROM sales";
$query = $dbh->prepare($sql);
$query->execute();
$sales = $query->fetchAll(PDO::FETCH_ASSOC);

// Calculate grand total (net)
$grandTotal = 0;
foreach ($sales as $row) {
    $grandTotal += (float)$row['Net_Amount'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sales Report</title>
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

    .print-btn {
      background-color: var(--primary);
      color: white;
      padding: 10px 16px;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      margin-bottom: 1rem;
    }

    .print-btn:hover {
      background-color: #b84343;
    }

    .total {
      font-size: 1.2rem;
      font-weight: bold;
      color: var(--text-dark);
    }

    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
        display: flex;
        justify-content: space-around;
        height: auto;
        flex-wrap: wrap;
      }

      .main-content {
        margin-left: 0;
        padding-top: 1rem;
      }

      table {
        font-size: 0.9rem;
      }
    }

    @media print {
      .sidebar,
      .print-btn,
      .header {
        display: none !important;
      }

      .main-content {
        margin: 0;
        padding: 0;
      }

      .container {
        box-shadow: none;
        padding: 0;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>FastBite</h2>
    <ul>
      <li><a href="admin-dashboard.php">Home</a></li>
      <li><a href="employee-database.php">Employees</a></li>
      <li><a href="requests.php" class="active">Requests</a></li>
      <li><a href="sales-report.php" class="active">Sales</a></li>
      <li><a href="inventory-report.php">Inventory</a></li>
      <li><a href="logout.php">Log out</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Sales Report</h1> 

    <button class="print-btn" onclick="window.print()">Print Report</button>

    <div class="container">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Sales ID</th>
            <th>Order ID</th>
            <th>Discount (%)</th>
            <th>Total Amount</th>
            <th>Net Amount</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($sales): ?>
            <?php foreach ($sales as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['Sale_Date']) ?></td>
                <td>#<?= htmlspecialchars($row['Sales_ID']) ?></td>
                <td><?= htmlspecialchars($row['Order_ID']) ?></td>
                <td><?= htmlspecialchars($row['Discount']) ?>%</td>
                <td>₱<?= number_format($row['Total_Amount'], 2) ?></td>
                <td>₱<?= number_format($row['Net_Amount'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6">No sales data available.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="total">Grand Total (Net): ₱<?= number_format($grandTotal, 2) ?></div>
    </div>
  </div>

</body>
</html>
