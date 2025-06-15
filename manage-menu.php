<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $name = trim($_POST['item_name']);
    $desc = trim($_POST['description']);
    $category = $_POST['category'];
    $price = floatval($_POST['price']);
    $availability = $_POST['availability'];
    $prep_time = intval($_POST['preparation_time']);

    if ($name && $category && $price > 0) {
        $stmt = $dbh->prepare("INSERT INTO menu (item_name, description, category, price, availability, preparation_time) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $category, $price, $availability, $prep_time]);
        header("Location: manage-menu.php");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $dbh->prepare("DELETE FROM menu WHERE item_id = ?");
    $stmt->execute([$id]);
    header("Location: manage-menu.php");
    exit;
}

$stmt = $dbh->query("SELECT * FROM menu ORDER BY category, item_name");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Menu - FastBite</title>
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
      margin-bottom: 1.5rem;
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
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background: #f3f3f3;
    }

    .action-btn {
      padding: 0.4rem 0.8rem;
      margin-right: 0.5rem;
      font-size: 0.9rem;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .edit-btn {
      background-color: #f0ad4e;
      color: white;
    }

    .delete-btn {
      background-color: #d9534f;
      color: white;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
        display: flex;
        justify-content: space-around;
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

<nav class="sidebar">
  <h2>FastBite</h2>
  <ul>
    <li><a href="employee-dashboard.php">Home</a></li>
    <li><a href="process-order.php">Process Order</a></li>
    <li><a href="employee-requests.php">Requests</a></li>
    <li><a href="manage-menu.php" class="active">Manage Menu</a></li>
    <li><a href="logout.php">Log out</a></li>
  </ul>
</nav>

<div class="main-content">
  <h1>Manage Menu</h1>

  <form method="POST" action="add-menu-item.php" style="margin-bottom: 1.5rem;">
    <input type="text" name="newName" placeholder="Item Name" required style="padding: 0.5rem; margin-right: 0.5rem;">
    <select name="newCategory" required style="padding: 0.5rem; margin-right: 0.5rem;">
      <option value="">Select Category</option>
      <option value="Burgers">Burgers</option>
      <option value="Fries & Sides">Fries & Sides</option>
      <option value="Drinks">Drinks</option>
      <option value="Desserts">Desserts</option>
    </select>
    <input type="number" name="newPrice" placeholder="Price" min="1" required style="padding: 0.5rem; margin-right: 0.5rem; width: 80px;">
    <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 4px;">Add</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Item Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
     <?php foreach ($items as $item): ?>

      <tr>
        <td><?= htmlspecialchars($item['item_id']) ?></td>
        <td><?= htmlspecialchars($item['item_name']) ?></td>
        <td><?= htmlspecialchars($item['category']) ?></td>
        <td>₱<?= number_format($item['price'], 2) ?></td>
        <td>
          <form method="POST" action="delete-menu-item.php" style="display:inline">
            <input type="hidden" name="id" value="<?= $item['item_id'] ?>">
            <button class="action-btn delete-btn" onclick="return confirm('Delete this item?')">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
