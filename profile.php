<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch customer profile info
$sql = "SELECT first_name, last_name, email, phone_number, street, city, postal_code, birthdate, registration_date FROM customer WHERE user_id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
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
        .profile-info dt {
            font-weight: bold;
            color: #cc5050;
            margin-top: 1rem;
        }
        .profile-info dd {
            margin-left: 0;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .profile-info .row-group {
            display: flex;
            gap: 1.5rem;
        }
        .profile-info .row-group > div {
            flex: 1;
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
            <dl class="profile-info">
                <dt>First Name</dt>
                <dd><?= htmlspecialchars($profile['first_name'] ?? '') ?></dd>

                <dt>Last Name</dt>
                <dd><?= htmlspecialchars($profile['last_name'] ?? '') ?></dd>

                <dt>Email</dt>
                <dd><?= htmlspecialchars($profile['email'] ?? '') ?></dd>

                <dt>Phone Number</dt>
                <dd><?= htmlspecialchars($profile['phone_number'] ?? '') ?></dd>

                <div class="row-group">
                    <div>
                        <dt>Street</dt>
                        <dd><?= htmlspecialchars($profile['street'] ?? '') ?></dd>
                    </div>
                    <div>
                        <dt>City</dt>
                        <dd><?= htmlspecialchars($profile['city'] ?? '') ?></dd>
                    </div>
                </div>

                <dt>Postal Code</dt>
                <dd><?= htmlspecialchars($profile['postal_code'] ?? '') ?></dd>

                <dt>Birthdate</dt>
                <dd><?= htmlspecialchars($profile['birthdate'] ?? '') ?></dd>

                <dt>Registration Date</dt>
                <dd><?= htmlspecialchars($profile['registration_date'] ?? '') ?></dd>
            </dl>
        </div>
    </div>
</body>
</html>