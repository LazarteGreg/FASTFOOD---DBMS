<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "fastfood");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $phone_number = $_POST['phone_number'];
  $birthdate = $_POST['birthdate'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password
  $street = $_POST['street'] ?? '';
  $city = $_POST['city'] ?? '';
  $postal_code = $_POST['postal_code'] ?? '';

  $insertUser = mysqli_prepare($conn, "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'customer')");
  if (!$insertUser) {
    $error = "Prepare failed for users: " . mysqli_error($conn);
  } else {
    mysqli_stmt_bind_param($insertUser, "sss", $username, $password, $email);
    try {
      if (!mysqli_stmt_execute($insertUser)) {
        if (mysqli_errno($conn) == 1062) {
          $error = "Email or username already exists.";
        } else {
          $error = "Error: " . mysqli_error($conn);
        }
      } else {
        $user_id = mysqli_insert_id($conn);
        $insertCustomer = mysqli_prepare($conn, "INSERT INTO customer (user_id, first_name, last_name, phone_number, birthdate, email, street, city, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$insertCustomer) {
          $error = "Prepare failed for customers: " . mysqli_error($conn);
        } else {
          mysqli_stmt_bind_param($insertCustomer, "issssssss", $user_id, $first_name, $last_name, $phone_number, $birthdate, $email, $street, $city, $postal_code);
          if (!mysqli_stmt_execute($insertCustomer)) {
            if (mysqli_errno($conn) == 1062) {
              $error = "Email already exists in customer table.";
            } else {
              $error = "Error: " . mysqli_error($conn);
            }
          } else {
            // Success: user_id is now always linked to customer
            header("Location: login.php?signup=success");
            exit();
          }
        }
      }
    } catch (mysqli_sql_exception $e) {
      if ($e->getCode() == 1062) {
        $error = "Email or username already exists.";
      } else {
        $error = "Error: " . $e->getMessage();
      }
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    body {
      height: 100vh;
      background: linear-gradient(to right, #cc5050, #d3c260);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-container {
      background-color: white;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      width: 400px;
    }

    .login-form h2 {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .input-group {
      margin-bottom: 1rem;
    }

    .input-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
    }

    .input-group input {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }

    button {
      width: 100%;
      padding: 0.75rem;
      background-color: #667eea;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #ad609a;
    }

    .signup-link {
      margin-top: 1rem;
      text-align: center;
      font-size: 0.9rem;
    }

    .signup-link a {
      color: #667eea;
      text-decoration: none;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

    .error-message {
      color: red;
      font-size: 0.9rem;
      margin-top: 1rem;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <form class="login-form" method="POST">
      <h2>Sign Up</h2>

      <div class="input-group">
        <label for="fname">First Name</label>
        <input type="text" name="first_name" id="fname" required />
      </div>

      <div class="input-group">
        <label for="lname">Last Name</label>
        <input type="text" name="last_name" id="lname" required />
      </div>

      <div class="input-group">
        <label for="contactnum">Contact Number</label>
        <input type="text" name="phone_number" id="contactnum" required />
      </div>

      <div class="input-group">
        <label for="bdate">Birthday</label>
        <input type="date" name="birthdate" id="bdate" required />
      </div>

      <div class="input-group">
        <label for="new-username">Username</label>
        <input type="text" name="username" id="new-username" required />
      </div>

      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required />
      </div>

      <div class="input-group">
        <label for="new-password">Password</label>
        <input type="password" name="password" id="new-password" required />
      </div>

      <div class="input-group">
        <label for="street">Street</label>
        <input type="text" name="street" id="street" required />
      </div>

      <div class="input-group">
        <label for="city">City</label>
        <input type="text" name="city" id="city" required />
      </div>

      <div class="input-group">
        <label for="postal_code">Postal Code</label>
        <input type="text" name="postal_code" id="postal_code" required />
      </div>

      <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>

      <button type="submit">Sign Up</button>
      <p class="signup-link">Already have an account? <a href="login.php">Login</a></p>
    </form>
  </div>
</body>
</html>
