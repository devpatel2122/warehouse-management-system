<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false]);
    exit();
}

// 1. Get sale header
$sale = $conn->query("SELECT s.*, c.name as customer_name, c.address as customer_address, c.phone as customer_phone, c.gstin as customer_gstin 
                      FROM sales s 
                      LEFT JOIN customers c ON s.customer_id = c.id 
                      WHERE s.id = $id")->fetch_assoc();

if (!$sale) {
    echo json_encode(['success' => false]);
    exit();
}

// 2. Get items
$itemsRes = $conn->query("SELECT si.*, p.name as product_name 
                          FROM sale_items si 
                          JOIN products p ON si.product_id = p.id 
                          WHERE si.sale_id = $id");

$items = [];
while ($row = $itemsRes->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode([
    'success' => true,
    'sale' => $sale,
    'items' => $items
]);
?>
