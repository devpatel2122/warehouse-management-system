<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$events = [];

// 1. Fetch Sales
$sales = $conn->query("SELECT s.id, s.total_amount, s.sale_date, c.name as customer_name 
                      FROM sales s 
                      LEFT JOIN customers c ON s.customer_id = c.id");
while($row = $sales->fetch_assoc()) {
    $events[] = [
        'title' => 'Sale: $' . $row['total_amount'] . ' (' . ($row['customer_name'] ?? 'Walk-in') . ')',
        'start' => $row['sale_date'],
        'color' => '#6366f1',
        'url' => '/warehouse/modules/sell/invoices.php'
    ];
}

// 2. Fetch Purchases
$purchases = $conn->query("SELECT p.id, p.total_amount, p.purchase_date, v.name as vendor_name 
                          FROM purchases p 
                          LEFT JOIN vendors v ON p.vendor_id = v.id");
while($row = $purchases->fetch_assoc()) {
    $events[] = [
        'title' => 'Purchase: $' . $row['total_amount'] . ' (' . ($row['vendor_name'] ?? 'Vendor') . ')',
        'start' => $row['purchase_date'],
        'color' => '#f43f5e',
        'url' => '/warehouse/modules/purchase/orders.php'
    ];
}

echo json_encode($events);
