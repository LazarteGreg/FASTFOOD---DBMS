<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? null;
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $street = $_POST['street'] ?? '';
    $city = $_POST['city'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';

    try {
        $sql = "UPDATE customer SET 
            first_name = ?, 
            last_name = ?, 
            email = ?, 
            phone_number = ?, 
            street = ?, 
            city = ?, 
            postal_code = ?, 
            birthdate = ? 
            WHERE customer_id = ?";
        
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            $first_name,
            $last_name,
            $email,
            $phone_number,
            $street,
            $city,
            $postal_code,
            $birthdate,
            $customer_id
        ]);
        
        header("Location: profile.php?success=1");
        exit;
    } catch (PDOException $e) {
        header("Location: profile.php?error=1");
        exit;
    }
} else {
    header("Location: profile.php");
    exit;
}
?>
