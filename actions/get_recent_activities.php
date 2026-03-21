<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

/**
 * We want a combined feed of:
 * 1. Sales
 * 2. Purchases
 * 3. New Products (if we want)
 */

$activities = [];

// Get Recent Sales
$sales = $conn->query("SELECT s.id, s.total_amount, s.created_at, c.name as customer_name 
                      FROM sales s 
                      LEFT JOIN customers c ON s.customer_id = c.id 
                      ORDER BY s.id DESC LIMIT 5");

while($row = $sales->fetch_assoc()) {
    $activities[] = [
        'type' => 'sale',
        'title' => 'Successfully Dispatched',
        'subtitle' => 'Customer: ' . ($row['customer_name'] ?? 'Walk-in'),
        'amount' => formatMoney($row['total_amount']),
        'time' => date('H:i', strtotime($row['created_at'])),
        'icon' => 'fa-shopping-cart',
        'color' => 'var(--success)',
        'timestamp' => strtotime($row['created_at'])
    ];
}

// Get Recent Purchases
$purchases = $conn->query("SELECT p.id, p.total_amount, p.created_at, v.name as vendor_name 
                          FROM purchases p 
                          LEFT JOIN vendors v ON p.vendor_id = v.id 
                          ORDER BY p.id DESC LIMIT 5");

while($row = $purchases->fetch_assoc()) {
    $activities[] = [
        'type' => 'purchase',
        'title' => 'Inventory Restocked',
        'subtitle' => 'Received from: ' . ($row['vendor_name'] ?? 'Vendor'),
        'amount' => formatMoney($row['total_amount']),
        'time' => date('H:i', strtotime($row['created_at'])),
        'icon' => 'fa-truck-loading',
        'color' => 'var(--primary)',
        'timestamp' => strtotime($row['created_at'])
    ];
}

// Sort by timestamp desc
usort($activities, function($a, $b) {
    return $b['timestamp'] - $a['timestamp'];
});

// Role-based data sanitization for granular privacy
$role = $_SESSION['role'];
foreach($activities as &$act) {
    if ($role == 'admin') continue; // Admin sees full details

    if ($role == 'product_dept' || $role == 'inventory_dept') {
        $act['amount'] = ''; // Hide all financial data
    } elseif ($role == 'sell_dept' && $act['type'] == 'purchase') {
        $act['amount'] = ''; // Sales staff don't see purchase costs
    } elseif ($role == 'purchase_dept' && $act['type'] == 'sale') {
        $act['amount'] = ''; // Purchase staff don't see sales revenue
    }
}

echo json_encode(array_slice($activities, 0, 8));
?>
