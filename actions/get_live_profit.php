<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) exit();

// Calculate total profit: Sum(sale_qty * (unit_price - purchase_price))
$query = "SELECT SUM(si.quantity * (si.unit_price - p.purchase_price)) as total_profit 
          FROM sale_items si 
          JOIN products p ON si.product_id = p.id";
$res = $conn->query($query);
$row = $res->fetch_assoc();

echo json_encode(['profit' => (float)($row['total_profit'] ?? 0)]);
?>
