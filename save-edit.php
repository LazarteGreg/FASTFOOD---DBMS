<?php
session_start();
include('includes/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
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
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
