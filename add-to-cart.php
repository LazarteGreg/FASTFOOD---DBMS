<?php
session_start();

$item_id = $_POST['item_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$item_id) {
    header('Location: menu.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['item_id'] == $item_id) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $_SESSION['cart'][] = [
        'item_id' => $item_id,
        'quantity' => $quantity
    ];
}

header('Location: cart.php');
exit;