<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ensure username is set for greeting
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - FastBite</title>
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
      min-height: 100vh;
      background-color: var(--bg-light);
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

    .card {
      background: white;
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      margin-bottom: 1.5rem;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
    }

    .grid-card {
      background: white;
      border-radius: 10px;
      padding: 1.2rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      text-align: center;
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .grid-card:hover {
      transform: scale(1.03);
      background: #ffece6;
    }

    .grid-card h2 {
      font-size: 1.1rem;
      color: var(--primary);
    }

    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
        display: flex;
        justify-content: space-around;
        height: auto;
        flex-direction: column;
      }

      .sidebar h2 {
        display: none;
      }

      .sidebar ul {
        display: flex;
        justify-content: space-around;
        width: 100%;
      }

      .main-content {
        margin-left: 0;
        padding-top: 1rem;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar">
  <h2>FastBite</h2>
  <ul>
    <li><a href="#" class="active">Home</a></li>
    <li><a href="employee-database.php">Employees</a></li>
    <li><a href="requests.php">Requests</a></li>
    <li><a href="sales-report.php">Sales</a></li>
    <li><a href="inventory-report.php">Inventory</a></li>
    <li><a href="logout.php">Log Out</a></li>
  </ul>
</nav>

<!-- Main content -->
<div class="main-content">
  <h1>Admin Dashboard</h1>

  <div class="card" style="text-align:center;">
    <h2 style="font-size:2rem; color:#cc5050; margin-bottom:0.5rem;">
      Welcome, <?php echo htmlspecialchars($username); ?>!
    </h2>
    <p style="font-size:1.1rem; color:#444;">
      Manage FastBite’s operations using the quick links below or the sidebar.<br>
      Have a productive day, Admin!
    </p>
  </div>

  <div class="dashboard-grid">
    <div class="grid-card" onclick="location.href='employee-database.php'">
      <h2>Employees' Database</h2>
    </div>

    <div class="grid-card" onclick="location.href='requests.php'">
      <h2>View Requests</h2>
    </div>

    <div class="grid-card" onclick="location.href='sales-report.php'">
      <h2>Sales</h2>
    </div>

    <div class="grid-card" onclick="location.href='inventory-report.php'">
      <h2>Inventory</h2>
    </div>
  </div>
</div>

</body>
</html>
