<?php
session_start();
include('includes/config.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle Add Employee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $employee_id = $_POST['employee_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact_number = $_POST['contact_number'];
    $role = $_POST['role'];
    $shift_timing = $_POST['shift_timing'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt1 = $dbh->prepare("INSERT INTO employees (employee_id, first_name, last_name, contact_number, role, shift_timing) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt1->execute([$employee_id, $first_name, $last_name, $contact_number, $role, $shift_timing]);

        $stmt2 = $dbh->prepare("INSERT INTO users (user_id, password, role) VALUES (?, ?, ?)");
        $stmt2->execute([$employee_id, $password, $role]);

        $message = "Employee added successfully.";
    } catch (PDOException $e) {
        $error = "Error adding employee: " . $e->getMessage();
    }
}

// Handle Delete Employee
if (isset($_GET['delete'])) {
    $emp_id = $_GET['delete'];

    try {
        $stmt1 = $dbh->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt1->execute([$emp_id]);

        $stmt2 = $dbh->prepare("DELETE FROM employees WHERE employee_id = ?");
        $stmt2->execute([$emp_id]);

        $message = "Employee deleted successfully.";
    } catch (PDOException $e) {
        $error = "Error deleting employee: " . $e->getMessage();
    }
}

// Handle Edit Employee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_employee'])) {
    $employee_id = $_POST['edit_employee_id'];
    $first_name = $_POST['edit_first_name'];
    $last_name = $_POST['edit_last_name'];
    $contact_number = $_POST['edit_contact_number'];
    $role = $_POST['edit_role'];
    $shift_timing = $_POST['edit_shift_timing'];

    try {
        $stmt = $dbh->prepare("UPDATE employees SET first_name=?, last_name=?, contact_number=?, role=?, shift_timing=? WHERE employee_id=?");
        $stmt->execute([$first_name, $last_name, $contact_number, $role, $shift_timing, $employee_id]);
        $message = "Employee updated successfully.";
    } catch (PDOException $e) {
        $error = "Error updating employee: " . $e->getMessage();
    }
}

// Fetch all employees
$sql = "SELECT * FROM employees";
$query = $dbh->prepare($sql);
$query->execute();
$employees = $query->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Employee Database</title>
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
      font-family: Segoe UI, sans-serif;
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

    h1 {
      margin-bottom: 1.5rem;
      color: var(--text-dark);
    }

    .actions {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 1rem;
    }

    .btn {
      background-color: var(--primary);
      color: white;
      padding: 0.5rem 1rem;
      margin-left: 0.5rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .btn:hover {
      background-color: #a04040;
    }

    .edit-btn {
      background-color: #ffc107;
      color: #333;
    }

    .edit-btn:hover {
      background-color: #e0a800;
    }

    .btn-add {
      background-color: #28a745;
      color: white;
      padding: 0.5rem 1rem;
      margin-left: 0.5rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .btn-add:hover {
      background-color: #218838;
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
      background-color: #f3f3f3;
    }

    .delete-btn {
      background-color: #dc3545;
    }

    .delete-btn:hover {
      background-color: #c82333;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
      background-color: #fff;
      margin: 5% auto;
      padding: 2rem;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
    }

    .modal-content h2 {
      margin-bottom: 1rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 0.3rem;
    }

    .form-group input {
      width: 100%;
      padding: 0.5rem;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover {
      color: black;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>FastBite</h2>
    <ul>
      <li><a href="admin-dashboard.php">Home</a></li>
      <li><a href="employee-database.php" class="active">Employees</a></li>
      <li><a href="requests.php">Requests</a></li>
      <li><a href="sales-report.php">Sales</a></li>
      <li><a href="inventory-report.php">Inventory</a></li>
      <li><a href="logout.php">Log out</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Employee Database</h1>

    <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <div class="actions">
      <button class="btn-add" onclick="document.getElementById('addModal').style.display='block'">Add Employee</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Employee ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Contact Number</th>
          <th>Role</th>
          <th>Shift Timing</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($employees): ?>
          <?php foreach ($employees as $emp): ?>
            <tr>
              <td><?= htmlspecialchars($emp['employee_id']) ?></td>
              <td><?= htmlspecialchars($emp['first_name']) ?></td>
              <td><?= htmlspecialchars($emp['last_name']) ?></td>
              <td><?= htmlspecialchars($emp['contact_number']) ?></td>
              <td><?= htmlspecialchars($emp['role']) ?></td>
              <td><?= htmlspecialchars($emp['shift_timing']) ?></td>
              <td>
                <button class="btn edit-btn" onclick="openEditModal('<?= $emp['employee_id'] ?>', '<?= htmlspecialchars($emp['first_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($emp['last_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($emp['contact_number'], ENT_QUOTES) ?>', '<?= htmlspecialchars($emp['role'], ENT_QUOTES) ?>', '<?= htmlspecialchars($emp['shift_timing'], ENT_QUOTES) ?>')">Edit</button>
                <a href="employee-database.php?delete=<?= $emp['employee_id'] ?>" onclick="return confirm('Are you sure?')">
                  <button class="btn delete-btn">Delete</button>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7">No employees found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Add Modal -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
      <h2>Add New Employee</h2>
      <form method="POST" action="">
        <input type="hidden" name="add_employee" value="1" />
        <div class="form-group">
          <label>Employee ID</label>
          <input type="text" name="employee_id" required />
        </div>
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="first_name" required />
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="last_name" required />
        </div>
        <div class="form-group">
          <label>Contact Number</label>
          <input type="text" name="contact_number" required />
        </div>
        <div class="form-group">
          <label>Role</label>
          <input type="text" name="role" required />
        </div>
        <div class="form-group">
          <label>Shift Timing</label>
          <input type="text" name="shift_timing" required />
        </div>
        <hr>
        <h3>Account Login</h3>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required />
        </div>
        <button type="submit" class="btn">Add Employee</button>
      </form>
    </div>
  </div>

  <!-- Edit Employee Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
      <h2>Edit Employee</h2>
      <form method="POST">
        <input type="hidden" name="edit_employee_id" id="edit_employee_id">
        <div class="form-group">
          <label for="edit_first_name">First Name</label>
          <input type="text" name="edit_first_name" id="edit_first_name" required>
        </div>
        <div class="form-group">
          <label for="edit_last_name">Last Name</label>
          <input type="text" name="edit_last_name" id="edit_last_name" required>
        </div>
        <div class="form-group">
          <label for="edit_contact_number">Contact Number</label>
          <input type="text" name="edit_contact_number" id="edit_contact_number" required>
        </div>
        <div class="form-group">
          <label for="edit_role">Role</label>
          <input type="text" name="edit_role" id="edit_role" required>
        </div>
        <div class="form-group">
          <label for="edit_shift_timing">Shift Timing</label>
          <input type="text" name="edit_shift_timing" id="edit_shift_timing" required>
        </div>
        <button type="submit" name="edit_employee" class="btn edit-btn">Save Changes</button>
      </form>
    </div>
  </div>

  <script>
    function openEditModal(id, first, last, contact, role, shift) {
      document.getElementById('edit_employee_id').value = id;
      document.getElementById('edit_first_name').value = first;
      document.getElementById('edit_last_name').value = last;
      document.getElementById('edit_contact_number').value = contact;
      document.getElementById('edit_role').value = role;
      document.getElementById('edit_shift_timing').value = shift;
      document.getElementById('editModal').style.display = 'block';
    }

    window.onclick = function(event) {
      var modal = document.getElementById('editModal');
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>
</html>
