<?php
session_start();
include('includes/config.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Approve/reject action (you can add a 'status' column to handle it)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancellation_id'], $_POST['action'])) {
    $cancellation_id = $_POST['cancellation_id'];
    $action = $_POST['action']; // Approved or Rejected

    // Add 'status' column if not yet in DB
    $update = $dbh->prepare("UPDATE order_cancellation SET status = ? WHERE cancellation_id = ?");
    $update->execute([$action, $cancellation_id]);
    $message = "Request has been $action.";
}

// Fetch all cancellation requests
$sql = "SELECT * FROM order_cancellation ORDER BY cancellation_date DESC";
$query = $dbh->prepare($sql);
$query->execute();
$requests = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Cancellations - Admin</title>
  <style>
    :root {
      --primary: #cc5050;
      --secondary: #d3c260;
      --bg-light: #fff8f0;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
    body { display: flex; background: var(--bg-light); min-height: 100vh; }

    .sidebar {
      width: 220px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      color: white;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      padding-top: 2rem;
    }

    .sidebar h2 { text-align: center; margin-bottom: 2rem; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar ul li { margin: 1rem 0; }
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
      margin-left: 220px;
      padding: 2rem;
      flex: 1;
    }

    h1 { margin-bottom: 1rem; }

    .message {
      background: #d4edda;
      color: #155724;
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #f8f8f8;
    }

    .btn {
      padding: 0.4rem 0.8rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 0.85rem;
      margin-right: 5px;
    }

    .approve { background: #28a745; color: white; }
    .reject { background: #dc3545; color: white; }

    .approve:hover { background: #218838; }
    .reject:hover { background: #c82333; }

    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
        flex-direction: row;
        flex-wrap: wrap;
        height: auto;
      }
      .main-content {
        margin-left: 0;
        padding-top: 1rem;
      }
      table {
        font-size: 0.9rem;
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
      <li><a href="sales-report.php">Sales</a></li>
      <li><a href="inventory-report.php">Inventory</a></li>
      <li><a href="cancellation-request.php" class="active">Cancellations</a></li>
      <li><a href="logout.php">Log out</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Order Cancellation Requests</h1>

    <?php if (!empty($message)): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>Cancellation ID</th>
          <th>Order ID</th>
          <th>Reason</th>
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($requests): ?>
          <?php foreach ($requests as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['cancellation_id']) ?></td>
              <td><?= htmlspecialchars($row['order_id']) ?></td>
              <td><?= htmlspecialchars($row['cancellation_reason']) ?></td>
              <td><?= htmlspecialchars($row['cancellation_date']) ?></td>
              <td><?= htmlspecialchars($row['status'] ?? 'Pending') ?></td>
              <td>
                <?php if (empty($row['status']) || $row['status'] === 'Pending'): ?>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="cancellation_id" value="<?= $row['cancellation_id'] ?>">
                    <input type="hidden" name="action" value="Approved">
                    <button class="btn approve" type="submit">Approve</button>
                  </form>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="cancellation_id" value="<?= $row['cancellation_id'] ?>">
                    <input type="hidden" name="action" value="Rejected">
                    <button class="btn reject" type="submit">Reject</button>
                  </form>
                <?php else: ?>
                  <em><?= htmlspecialchars($row['status']) ?></em>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">No cancellation requests found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
