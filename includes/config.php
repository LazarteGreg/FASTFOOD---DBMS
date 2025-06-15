<?php
$host = 'localhost';
$dbname = 'fastfood';
$username = 'root'; // or your DB username
$password = '';     // your DB password (empty by default for XAMPP)

try {
    $dbh = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>