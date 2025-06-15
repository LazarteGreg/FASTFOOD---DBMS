<?php
session_start();
include('includes/config.php');

// Redirect if not logged in as employee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$shiftTiming = 'No shift assigned';
$sql = "SELECT shift_timing FROM employees WHERE user_id = :user_id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $shiftTiming = $row['shift_timing'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Employee Dashboard</title>
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
      color: var(--text-dark);
      margin-bottom: 1rem;
    }

    .card-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .card h2 {
      font-size: 1.2rem;
      color: var(--primary);
      margin-bottom: 0.8rem;
    }

    .card p {
      color: var(--text-dark);
      font-size: 0.95rem;
      flex: 1;
    }

    .card button {
      margin-top: 1rem;
      padding: 0.5rem 1rem;
      border: none;
      background-color: var(--primary);
      color: white;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 0.9rem;
    }

    .card button:hover {
      background-color: #a04040;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
      }

      .main-content {
        margin-left: 0;
        padding-top: 1rem;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>FastBite</h2>
    <ul>
      <li><a href="employee-dashboard.php" class="active">Home</a></li>
      <li><a href="process-order.php">Process Order</a></li>
      <li><a href="employee-requests.php">Requests</a></li>
      <li><a href="manage-menu.php">Manage Menu</a></li>
      <li><a href="logout.php">Log out</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Welcome, Employee!</h1>

    <div class="card-container">
      <div class="card">
        <h2>Shift Schedule</h2>
        <p>Today: <?php echo htmlspecialchars($shiftTiming); ?></p>
        <button onclick="alert('Viewing shift schedule...')">View Shift</button>
      </div>

      <div class="card">
        <h2>Pending Tasks</h2>
        <p>3 orders to prepare</p>
        <button onclick="location.href='process-order.php'">Manage Orders</button>
      </div>

      <div class="card">
        <h2>Announcements</h2>
        <p>Team meeting at 5:00 PM in the staff room.</p>
        <button onclick="alert('Reading announcements...')">Read More</button>
      </div>

      <div class="card">
        <h2>Manage Menu</h2>
        <p>Update or edit items on the current menu.</p>
        <button onclick="location.href='manage-menu.php'">Go to Menu</button>
      </div>
    </div>
  </div>

</body>
</html>
