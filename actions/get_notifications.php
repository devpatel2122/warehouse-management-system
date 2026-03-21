<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

// Fetch products where stock is at or below their individual reorder level
$query = "SELECT id, name, stock_quantity, reorder_level FROM products WHERE stock_quantity <= reorder_level LIMIT 10";
$res = $conn->query($query);
$notifications = [];
while ($row = $res->fetch_assoc()) {
    $notifications[] = [
        'title' => 'Low Stock Alert',
        'message' => $row['name'] . ' is low on stock (' . $row['stock_quantity'] . ' left).',
        'link' => '/warehouse/modules/product/inventory.php'
    ];
}

echo json_encode($notifications);
?>
