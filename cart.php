<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

// Handle quantity update or remove actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_qty'], $_POST['item_id'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['item_id'] == $_POST['item_id']) {
                $delta = intval($_POST['change_qty']);
                $newQty = max(1, $item['quantity'] + $delta);
                $item['quantity'] = $newQty;
                break;
            }
        }
        unset($item);
    }
    if (isset($_POST['remove_item'], $_POST['item_id'])) {
        foreach ($_SESSION['cart'] as $i => $item) {
            if ($item['item_id'] == $_POST['item_id']) {
                array_splice($_SESSION['cart'], $i, 1);
                break;
            }
        }
    }
}

$cart = $_SESSION['cart'] ?? [];
$itemDetails = [];
$total = 0;

if (!empty($cart)) {
    $itemIds = array_column($cart, 'item_id');
    $placeholders = implode(',', array_fill(0, count($itemIds), '?'));

    $stmt = $dbh->prepare("SELECT * FROM menu WHERE item_id IN ($placeholders)");
    $stmt->execute($itemIds);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        foreach ($cart as $item) {
            if ($item['item_id'] == $row['item_id']) {
                $row['quantity'] = $item['quantity'];
                $row['subtotal'] = $item['quantity'] * $row['price'];
                $total += $row['subtotal'];
                $itemDetails[] = $row;
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Cart - FastBite</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --sidebar-width: 220px;
      --primary: #cc5050;
      --secondary: #d3c260;
      --bg-light: #f9f9f9;
      --text-dark: #333;
    }
    body {
      display: flex;
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: var(--bg-light);
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
    .sidebar ul li a {
      color: white;
      text-decoration: none;
      padding: 1rem 1.5rem;
      display: block;
    }
    .main {
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
      margin-bottom: 1.5rem;
      background: white;
      border-radius: 8px;
      overflow: hidden;
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th.qty, td.qty {
      text-align: center;
    }
    .total {
      text-align: right;
      font-size: 1.2rem;
      font-weight: bold;
    }
    button {
      background-color: var(--primary);
      color: white;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9rem;
    }
    button.qty-btn {
      padding: 0.3rem 0.7rem;
      font-size: 1.1rem;
      margin: 0 2px;
      background: #eee;
      color: #333;
      border: 1px solid #ccc;
    }
    button.qty-btn:hover {
      background: #ddd;
      color: var(--primary);
    }
    .qty-input {
      width: 40px;
      text-align: center;
      padding: 4px;
      border-radius: 4px;
      border: 1px solid #ccc;
      margin: 0 2px;
      background: #f9f9f9;
    }
  </style>
</head>
<body>

<nav class="sidebar">
  <h2>FastBite</h2>
  <ul>
    <li><a href="customer-dashboard.php">Home</a></li>
    <li><a href="menu.php">Menu</a></li>
    <li><a href="track-order.php">Orders</a></li>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</nav>

<div class="main">
  <h1>My Cart</h1>

  <form action="payment.php" method="post">
    <table>
      <thead>
        <tr>
          <th>Item</th>
          <th>Price</th>
          <th class="qty">Qty</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($itemDetails)): ?>
          <tr>
            <td colspan="5" style="text-align:center;">Your cart is empty.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($itemDetails as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['item_name']) ?></td>
              <td>₱<?= number_format($item['price'], 2) ?></td>
              <td class="qty">
                <form action="cart.php" method="post" style="display:inline;">
                  <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                  <button type="submit" name="change_qty" value="-1" class="qty-btn">−</button>
                  <input type="text" class="qty-input" value="<?= $item['quantity'] ?>" readonly>
                  <button type="submit" name="change_qty" value="1" class="qty-btn">+</button>
                </form>
              </td>
              <td>₱<?= number_format($item['subtotal'], 2) ?></td>
              <td>
                <form action="cart.php" method="post" style="display:inline;">
                  <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                  <button type="submit" name="remove_item">Remove</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="total">Total: ₱<?= number_format($total, 2) ?></div>
    <?php if (!empty($itemDetails)): ?>
      <button type="submit">Proceed to Payment</button>
    <?php endif; ?>
  </form>
</div>

</body>
</html>
