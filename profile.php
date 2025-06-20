<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch customer profile info
$sql = "SELECT customer_id, first_name, last_name, email, phone_number, street, city, postal_code, birthdate, registration_date FROM customer WHERE user_id = ?";
try {
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
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
            background: rgba(255,255,255,0.2);
            border-left: 4px solid white;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            flex: 1;
        }
        .profile-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px #0001;
            padding: 2rem 2.5rem;
        }
        h1 {
            color: #cc5050;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .profile-info {
            margin-top: 1.5rem;
        }
        .profile-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .profile-item label {
            font-weight: bold;
            color: #cc5050;
            min-width: 120px;
            margin-right: 1rem;
        }
        .profile-item input {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .profile-item input:focus {
            outline: none;
            border-color: #cc5050;
        }
        .submit-btn {
            background-color: #cc5050;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .submit-btn:hover {
            background-color: #b34040;
        }
        
        /* Confirmation dialog styles */
        .confirmation-dialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .confirmation-dialog.active {
            display: block;
        }
        .confirmation-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .confirmation-overlay.active {
            display: block;
        }
        .confirmation-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .confirmation-button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .confirmation-button.confirm {
            background-color: #cc5050;
            color: white;
        }
        .confirmation-button.cancel {
            background-color: #ddd;
            color: #333;
        }
        .confirmation-button:hover {
            opacity: 0.9;
        }
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .profile-form {
            width: 100%;
        }
        
        /* Order History Styles */
        .order-history {
            margin-top: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .order-history h2 {
            color: #cc5050;
            margin-bottom: 1.5rem;
        }
        .orders-list {
            display: grid;
            gap: 1rem;
        }
        .order-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-details h3 {
            color: #333;
            margin: 0 0 0.5rem 0;
        }
        .order-details p {
            margin: 0.25rem 0;
            color: #666;
        }
        .order-details strong {
            color: #333;
        }
        .view-receipt-btn {
            background-color: #cc5050;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .view-receipt-btn:hover {
            background-color: #b34040;
        }
        .no-orders {
            color: #666;
            text-align: center;
            padding: 2rem;
        }
        .error {
            color: #dc3545;
            text-align: center;
            padding: 1rem;
        }
        @media (max-width: 900px) {
            .main-content { padding: 1rem; }
            .sidebar { width: 100px; }
            .sidebar h2 { font-size: 1rem; }
            .sidebar ul li a { padding: 0.5rem 0.5rem; font-size: 0.9rem; }
            .main-content { margin-left: 100px; }
        }
        @media (max-width: 600px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <h2>FastBite</h2>
        <ul>
            <li><a href="customer-dashboard.php">Home</a></li>
            <li><a href="profile.php" class="active">My Profile</a></li>
            <li><a href="customer-orders.php">My Orders</a></li>
            <li><a href="logout.php">Log out</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <div class="profile-container">
            <h1>My Profile</h1>
            <div class="profile-info">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert success">
                        Profile updated successfully!
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert error">
                        An error occurred while updating your profile.
                    </div>
                <?php endif; ?>
                
                <form action="update-profile.php" method="POST" class="profile-form">
                    <input type="hidden" name="customer_id" value="<?= htmlspecialchars($profile['customer_id']) ?>">
                    <div class="profile-item">
                        <label>First Name:</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="profile-item">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" required>
                    </div>
                    <div class="profile-item">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required>
                    </div>
                    <div class="profile-item">
                        <label>Phone Number:</label>
                        <input type="tel" name="phone_number" value="<?= htmlspecialchars($profile['phone_number'] ?? '') ?>">
                    </div>
                    <div class="profile-item">
                        <label>Street:</label>
                        <input type="text" name="street" value="<?= htmlspecialchars($profile['street'] ?? '') ?>">
                    </div>
                    <div class="profile-item">
                        <label>City:</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($profile['city'] ?? '') ?>">
                    </div>
                    <div class="profile-item">
                        <label>Postal Code:</label>
                        <input type="text" name="postal_code" value="<?= htmlspecialchars($profile['postal_code'] ?? '') ?>">
                    </div>
                    <div class="profile-item">
                        <label>Birthdate:</label>
                        <input type="date" name="birthdate" value="<?= htmlspecialchars($profile['birthdate'] ?? '') ?>">
                    </div>
                    <div class="profile-item">
                        <label>Registration Date:</label>
                        <span><?= htmlspecialchars($profile['registration_date'] ?? '') ?></span>
                    </div>
                    <div class="profile-item">
                        <button type="button" id="confirmUpdate" class="submit-btn">Update Profile</button>
                    </div>
                    
                    <!-- Confirmation Dialog -->
                    <div class="confirmation-overlay" id="overlay">
                        <div class="confirmation-dialog" id="dialog">
                            <h3>Confirm Update</h3>
                            <p>Are you sure you want to update your profile?</p>
                            <div class="confirmation-buttons">
                                <button type="button" class="confirmation-button confirm" id="confirmYes">Yes</button>
                                <button type="button" class="confirmation-button cancel" id="confirmNo">No</button>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const confirmBtn = document.getElementById('confirmUpdate');
                            const overlay = document.getElementById('overlay');
                            const dialog = document.getElementById('dialog');
                            const confirmYes = document.getElementById('confirmYes');
                            const confirmNo = document.getElementById('confirmNo');
                            const form = document.querySelector('.profile-form');

                            confirmBtn.addEventListener('click', function() {
                                overlay.classList.add('active');
                                dialog.classList.add('active');
                            });

                            confirmYes.addEventListener('click', function() {
                                form.submit();
                            });

                            confirmNo.addEventListener('click', function() {
                                overlay.classList.remove('active');
                                dialog.classList.remove('active');
                            });

                            // Close dialog when clicking outside
                            overlay.addEventListener('click', function(e) {
                                if (e.target === overlay) {
                                    overlay.classList.remove('active');
                                    dialog.classList.remove('active');
                                }
                            });
                        });
                    </script>
                </form>
            </div>
            

        </div>
    </div>
</body>
</html>