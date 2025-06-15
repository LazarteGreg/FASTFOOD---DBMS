<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
  header('Location: login.html');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home - FastBite</title>
  <style>
    :root {
      --sidebar-width: 220px;
      --primary: #cc5050;
      --secondary: #d3c260;
      --light-bg: #fff8f0;
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
      background-color: var(--light-bg);
      min-height: 100vh;
    }

    .sidebar {
      width: var(--sidebar-width);
      background: linear-gradient(to right, var(--primary), var(--secondary));
      color: white;
      padding: 2rem 1rem;
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
    }

    .sidebar ul li {
      margin: 1rem 0;
    }

    .sidebar ul li a {
      color: white;
      text-decoration: none;
      padding: 0.75rem 1rem;
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

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s;
    }

    .card:hover {
      transform: scale(1.02);
    }

    .card h2 {
      margin-bottom: 0.5rem;
      color: var(--primary);
    }

    .card p {
      color: #555;
    }

    button {
      margin-top: 1rem;
      padding: 0.5rem 1rem;
      background-color: #cc5050;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background-color: #b03e3e;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        position: static;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        height: auto;
      }

      .sidebar h2 {
        display: none;
      }

      .sidebar ul {
        display: flex;
        width: 100%;
        justify-content: space-around;
      }

      .main-content {
        margin-left: 0;
        padding-top: 1rem;
      }
    }
  </style>
</head>
<body>

<nav class="sidebar">
  <h2>FastBite</h2>
  <ul>
    <li><a href="#" class="active">Home</a></li>
    <li><a href="menu.php">Menu</a></li>
    <li><a href="track-order.php">Orders</a></li>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</nav>

<div class="main-content">
  <h1>Welcome to FastBite!</h1>
  <div class="dashboard-grid">
    <div class="card">
      <h2>View Menu</h2>
      <p>Explore our mouth-watering burgers, sides, drinks, and desserts available for order.</p>
      <button onclick="location.href='menu.php'">Browse Menu</button>
    </div>
    <div class="card">
      <h2>Track Orders</h2>
      <p>Check the status of your recent orders in real-time. See what's being prepared or on the way!</p>
      <button onclick="location.href='track-order.php'">View My Orders</button>
    </div>
  </div>
</div>

</body>
</html>