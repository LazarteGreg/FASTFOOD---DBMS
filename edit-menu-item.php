<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $name = trim($_POST['edit_item_name']);
    $desc = trim($_POST['edit_description']);
    $category = $_POST['edit_category'];
    $price = floatval($_POST['edit_price']);

    if ($name && $category && $price > 0) {
        try {
            $stmt = $dbh->prepare("UPDATE menu SET item_name = ?, description = ?, category = ?, price = ? WHERE item_id = ?");
            $stmt->execute([$name, $desc, $category, $price, $id]);
            header("Location: manage-menu.php?success=1");
            exit;
        } catch (Exception $e) {
            header("Location: manage-menu.php?error=1");
            exit;
        }
    }
}

header("Location: manage-menu.php");
exit;
?>
