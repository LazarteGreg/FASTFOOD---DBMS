<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "fastfood";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $user = $_POST['username'];
  $pass = $_POST['password'];


  $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE username = ?");
  $stmt->bind_param("s", $user);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($pass, $row['password'])) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $user;
        $_SESSION['role'] = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: admin-dashboard.php");
        } else if ($row['role'] === 'customer') {
            header("Location: customer-dashboard.php");
        } else if ($row['role'] === 'employee') {
            header("Location: employee-dashboard.php");
        } else {
            echo "<script>alert('Unknown role. Please contact support.');</script>";
        }
        exit();
    } else {
        echo "<script>alert('Incorrect password.');</script>";
    }
} else {
    echo "<script>alert('Username not found.');</script>";
}

  $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      height: 100vh;
      background: linear-gradient(135deg, #cc5050, #d3c260);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-container {
      background: #fff;
      padding: 2rem 2.5rem;
      border-radius: 16px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
    }

    .login-form h2 {
      text-align: center;
      margin-bottom: 1.8rem;
      color: #333;
    }

    .input-group {
      margin-bottom: 1.2rem;
    }

    .input-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #444;
    }

    .input-group input {
      width: 100%;
      padding: 0.65rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      transition: border-color 0.3s;
    }

    .input-group input:focus {
      border-color: #667eea;
      outline: none;
    }

    button {
      width: 100%;
      padding: 0.8rem;
      font-size: 1rem;
      background-color: #667eea;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #5762d5;
    }

    .signup-link {
      text-align: center;
      margin-top: 1.2rem;
      font-size: 0.9rem;
      color: #555;
    }

    .signup-link a {
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <form class="login-form" method="POST" action="">
      <h2>Login</h2>

      <div class="input-group">
        <label for="username">Username</label>
        <input type="text" name="username" required />
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" required />
      </div>

      <button type="submit">Login</button>
      <p class="signup-link">Don't have an account? <a href="signup.php">Sign up</a></p>
    </form>
  </div>
</body>
</html>
