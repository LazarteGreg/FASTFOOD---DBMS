<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
  header('Location: login.php');
  exit;
}

$menuItems = [];

try {
  $stmt = $dbh->prepare("SELECT * FROM menu ORDER BY category, item_name");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($result as $row) {
    $category = $row['category'];
    $menuItems[$category][] = $row;
  }
} catch (PDOException $e) {
  die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Browse Menu - FastBite</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --sidebar-width: 220px;
      --primary: #cc5050;
      --secondary: #d3c260;
      --light-bg: #f5f7fa;
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
      background-color: var(--light-bg);
    }

    .sidebar {
      width: var(--sidebar-width);
      background: linear-gradient(to right, var(--primary), var(--secondary));
      color: white;
      padding-top: 2rem;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
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

    .menu-section {
      margin-bottom: 2.5rem;
    }

    .menu-section h2 {
      margin-bottom: 1rem;
      color: var(--primary);
      border-bottom: 2px solid #eee;
      padding-bottom: 0.5rem;
    }

    .menu-items {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
    }

    .menu-card {
      background: white;
      border-radius: 10px;
      padding: 1rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      text-align: center;
      transition: transform 0.2s ease;
    }

    .menu-card:hover {
      transform: scale(1.02);
    }

    .menu-card img {
      width: 100%;
      height: 140px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 0.75rem;
    }

    .menu-card h3 {
      font-size: 1.1rem;
      margin-bottom: 0.3rem;
      color: var(--text-dark);
    }

    .menu-card p {
      font-size: 0.9rem;
      color: #666;
    }

    .menu-card .price {
      color: var(--primary);
      font-weight: bold;
      margin: 0.5rem 0;
    }

    .menu-card button {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .menu-card button:hover {
      background: #b84444;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
        display: flex;
        justify-content: space-around;
        height: auto;
        padding: 1rem 0;
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

<!-- Sidebar -->
<nav class="sidebar">
  <h2>FastBite</h2>
  <ul>
    <li><a href="customer-dashboard.php">Home</a></li>
    <li><a href="#" class="active">Menu</a></li>
    <li><a href="track-order.php">Orders</a></li>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</nav>

<!-- Main Content -->
<div class="main-content">
    <?php if (isset($_GET['added_to_cart'])): ?>
    <div class="success-message" style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
        Item added to cart successfully! <a href="cart.php" style="color: var(--primary); text-decoration: underline;">View Cart</a>
    </div>
    <?php endif; ?>
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Browse Our Menu</h1>
    <button onclick="window.location.href='cart.php'" style="background: var(--primary); color: white; border: none; padding: 0.6rem 1rem; border-radius: 5px; cursor: pointer; font-size: 0.9rem;">
      <i class="fas fa-shopping-cart"></i> View Cart
    </button>
  </div>

  <?php foreach ($menuItems as $category => $items): ?>
    <div class="menu-section">
      <h2><?= htmlspecialchars($category) ?></h2>
      <div class="menu-items">
        <?php foreach ($items as $item): ?>
          <div class="menu-card">
            <img src="uploads/<?= htmlspecialchars($item['images']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
            <h3><?= htmlspecialchars($item['item_name']) ?></h3>
            <p><?= htmlspecialchars($item['description']) ?></p>
            <div class="price">₱<?= number_format($item['price'], 2) ?></div>
            <form action="add-to-cart.php" method="post" style="margin-top: 0.5rem;">
              <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['item_id']) ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit">Add to Cart</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

</body>
</html>