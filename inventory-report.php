<?php
session_start();
include('includes/config.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Declare unit ranges for each status
define('OUT_OF_STOCK_THRESHOLD', 0);    // 0 units: Out of Stock
define('LOW_STOCK_MIN', 1);             // 1 unit is the minimum for Low Stock
define('LOW_STOCK_MAX', 20);            // 1-20 units: Low Stock
define('IN_STOCK_MIN', 21);             // 21+ units: In Stock

// Join inventory with products table
$sql = "SELECT 
            i.Inventory_ID,
            i.Quantity,
            i.Stock_Status,
            i.Last_Updated,
            p.Product_ID,
            p.Product_Name,
            p.Unit,
            p.Category,
            p.Expiration_Date
        FROM inventory i
        LEFT JOIN products p ON i.Product_ID = p.Product_ID";
$query = $dbh->prepare($sql);
$query->execute();
$items = $query->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['add_inventory'])) {
    $name = $_POST['add_product_name'];
    $qty = (int)$_POST['add_quantity'];
    $unit = $_POST['add_unit'];
    $cat = $_POST['add_category'];
    $exp = $_POST['add_expiration'];
    // Insert into products table
    $stmt = $dbh->prepare("INSERT INTO products (Product_Name, Unit, Category, Expiration_Date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $unit, $cat, $exp]);
    $product_id = $dbh->lastInsertId();
    // Insert into inventory table
    $stmt2 = $dbh->prepare("INSERT INTO inventory (Product_ID, Quantity, Stock_Status, Last_Updated) VALUES (?, ?, ?, NOW())");
    $status = $qty == 0 ? 'Out of Stock' : ($qty < 25 ? 'Low on Stock' : 'In Stock');
    $stmt2->execute([$product_id, $qty, $status]);
    echo "<script>alert('Inventory added successfully!'); window.location.href='inventory-report.php';</script>";
    exit;
}

if (isset($_POST['edit_inventory'])) {
    $id = $_POST['edit_inventory_id'];
    $name = $_POST['edit_product_name'];
    $qty = (int)$_POST['edit_quantity'];
    $unit = $_POST['edit_unit'];
    $cat = $_POST['edit_category'];
    $exp = $_POST['edit_expiration'];
    // Update products table
    $stmt = $dbh->prepare("UPDATE products p JOIN inventory i ON p.Product_ID = i.Product_ID SET p.Product_Name=?, p.Unit=?, p.Category=?, p.Expiration_Date=? WHERE i.Inventory_ID=?");
    $stmt->execute([$name, $unit, $cat, $exp, $id]);
    // Update inventory table
    $status = $qty == 0 ? 'Out of Stock' : ($qty < 25 ? 'Low on Stock' : 'In Stock');
    $stmt2 = $dbh->prepare("UPDATE inventory SET Quantity=?, Stock_Status=?, Last_Updated=NOW() WHERE Inventory_ID=?");
    $stmt2->execute([$qty, $status, $id]);
    echo "<script>alert('Inventory updated successfully!'); window.location.href='inventory-report.php';</script>";
    exit;
}
if (isset($_POST['delete_inventory_id'])) {
    $id = $_POST['delete_inventory_id'];
    // Delete from inventory, then from products (if not referenced elsewhere)
    $stmt = $dbh->prepare("SELECT Product_ID FROM inventory WHERE Inventory_ID=?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($prod) {
        $product_id = $prod['Product_ID'];
        $dbh->prepare("DELETE FROM inventory WHERE Inventory_ID=?")->execute([$id]);
        // Only delete product if not used in other inventory rows
        $check = $dbh->prepare("SELECT COUNT(*) FROM inventory WHERE Product_ID=?");
        $check->execute([$product_id]);
        if ($check->fetchColumn() == 0) {
            $dbh->prepare("DELETE FROM products WHERE Product_ID=?")->execute([$product_id]);
        }
    }
    echo "<script>alert('Inventory deleted successfully!'); window.location.href='inventory-report.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Inventory Report</title>
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

    .status-instock {
      color: #28a745;
      font-weight: bold;
    }
    .status-lowstock {
      color: #ffc107;
      font-weight: bold;
    }
    .status-outstock {
      color: #dc3545;
      font-weight: bold;
    }
    .status-unknown {
      color: #6c757d;
      font-weight: bold;
    }

    @media print {
      .sidebar,
      .print-btn {
        display: none !important;
      }

      .main-content {
        margin: 0;
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
      <li><a href="sales-report.php">Sales</a></li>
      <li><a href="inventory-report.php" class="active">Inventory</a></li>
      <li><a href="logout.php">Log out</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Inventory Report</h1>
    <button class="print-btn" onclick="window.print()">Print Report</button>
    <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 1rem;">
      <button class="add-btn" onclick="openAddModal()" style="background:#28a745; color:white; border:none; border-radius:6px; padding:10px 20px; font-size:1rem; cursor:pointer;">+ Add Inventory</button>
    </div>

    <!-- Add Inventory Modal -->
    <div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000; justify-content:center; align-items:center;">
      <form method="POST" style="background:white; padding:2rem; border-radius:10px; min-width:320px; box-shadow:0 2px 8px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:1rem; align-items:center;">
        <h2>Add Inventory</h2>
        <label for="add_product_name" style="align-self:flex-start;">Product Name</label>
        <input name="add_product_name" id="add_product_name" placeholder="Product Name" required style="padding:8px; width:100%;">
        <label for="add_quantity" style="align-self:flex-start;">Quantity</label>
        <input name="add_quantity" id="add_quantity" type="number" placeholder="Quantity" min="0" required style="padding:8px; width:100%;">
        <label for="add_unit" style="align-self:flex-start;">Unit</label>
        <input name="add_unit" id="add_unit" placeholder="Unit" required style="padding:8px; width:100%;">
        <label for="add_category" style="align-self:flex-start;">Category</label>
        <input name="add_category" id="add_category" placeholder="Category" required style="padding:8px; width:100%;">
        <label for="add_expiration" style="align-self:flex-start;">Expiration Date</label>
        <input name="add_expiration" id="add_expiration" type="date" placeholder="Expiration Date" required style="padding:8px; width:100%;">
        <button type="submit" name="add_inventory" style="background:#28a745; color:white; border:none; border-radius:6px; padding:10px 20px; font-size:1rem;">Add</button>
        <button type="button" onclick="closeAddModal()" style="background:#ccc; color:#333; border:none; border-radius:6px; padding:8px 18px; font-size:1rem; margin-top:8px;">Cancel</button>
      </form>
    </div>

    <!-- Edit Inventory Modal -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000; justify-content:center; align-items:center;">
      <form method="POST" style="background:white; padding:2rem; border-radius:10px; min-width:320px; box-shadow:0 2px 8px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:1rem; align-items:center;">
        <h2>Edit Inventory</h2>
        <input type="hidden" name="edit_inventory_id" id="edit_inventory_id">
        <label for="edit_product_name" style="align-self:flex-start;">Product Name</label>
        <input name="edit_product_name" id="edit_product_name" placeholder="Product Name" required style="padding:8px; width:100%;">
        <label for="edit_quantity" style="align-self:flex-start;">Quantity</label>
        <input name="edit_quantity" id="edit_quantity" type="number" placeholder="Quantity" min="0" required style="padding:8px; width:100%;">
        <label for="edit_unit" style="align-self:flex-start;">Unit</label>
        <input name="edit_unit" id="edit_unit" placeholder="Unit" required style="padding:8px; width:100%;">
        <label for="edit_category" style="align-self:flex-start;">Category</label>
        <input name="edit_category" id="edit_category" placeholder="Category" required style="padding:8px; width:100%;">
        <label for="edit_expiration" style="align-self:flex-start;">Expiration Date</label>
        <input name="edit_expiration" id="edit_expiration" type="date" placeholder="Expiration Date" required style="padding:8px; width:100%;">
        <button type="submit" name="edit_inventory" style="background:#d3c260; color:#333; border:none; border-radius:6px; padding:10px 20px; font-size:1rem;">Save</button>
        <button type="button" onclick="closeEditModal()" style="background:#ccc; color:#333; border:none; border-radius:6px; padding:8px 18px; font-size:1rem; margin-top:8px;">Cancel</button>
      </form>
    </div>

    <div class="container">
      <table>
        <thead>
          <tr>
            <th>Inventory ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Category</th>
            <th>Status</th>
            <th>Expiration Date</th>
            <th>Last Updated</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($items): ?>
            <?php foreach ($items as $item): ?>
              <?php
                $quantity = (int)$item['Quantity'];
                if ($quantity == 0) {
                    $stock_status = 'Out of Stock';
                } elseif ($quantity < 25) {
                    $stock_status = 'Low on Stock';
                } elseif ($quantity >= 25) {
                    $stock_status = 'In Stock';
                } else {
                    $stock_status = 'Unknown';
                }
              ?>
              <tr>
                <td><?= htmlspecialchars($item['Inventory_ID']) ?></td>
                <td><?= htmlspecialchars($item['Product_Name']) ?></td>
                <td><?= htmlspecialchars($item['Quantity']) ?></td>
                <td><?= htmlspecialchars($item['Unit']) ?></td>
                <td><?= htmlspecialchars($item['Category']) ?></td>
                <td><?= $stock_status ?></td>
                <td><?= htmlspecialchars($item['Expiration_Date']) ?></td>
                <td><?= htmlspecialchars($item['Last_Updated']) ?></td>
                <td style="white-space:nowrap;">
                  <button class="edit-btn" onclick="openEditModal(<?= $item['Inventory_ID'] ?>, '<?= htmlspecialchars(addslashes($item['Product_Name'])) ?>', <?= $item['Quantity'] ?>, '<?= htmlspecialchars(addslashes($item['Unit'])) ?>', '<?= htmlspecialchars(addslashes($item['Category'])) ?>', '<?= $item['Expiration_Date'] ?>')" style="background:#d3c260; color:#333; border:none; border-radius:5px; padding:6px 14px; margin-right:4px; cursor:pointer;">Edit</button>
                  <button class="delete-btn" onclick="confirmDelete(<?= $item['Inventory_ID'] ?>)" style="background:#cc5050; color:white; border:none; border-radius:5px; padding:6px 14px; margin-right:4px; cursor:pointer;">Delete</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="9">No inventory data available.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function openAddModal() {
      document.getElementById('addModal').style.display = 'flex';
    }
    function closeAddModal() {
      document.getElementById('addModal').style.display = 'none';
    }
    function openEditModal(id, name, qty, unit, cat, exp) {
      document.getElementById('edit_inventory_id').value = id;
      document.getElementById('edit_product_name').value = name;
      document.getElementById('edit_quantity').value = qty;
      document.getElementById('edit_unit').value = unit;
      document.getElementById('edit_category').value = cat;
      document.getElementById('edit_expiration').value = exp;
      document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }
    function confirmDelete(id) {
      if (confirm('Are you sure you want to delete this inventory item?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="delete_inventory_id" value="'+id+'">';
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>

</body>
</html>
