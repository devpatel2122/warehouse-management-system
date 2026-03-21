<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$q = $conn->real_escape_string($_GET['q'] ?? '');

$query = "SELECT id, name, price, stock_quantity, barcode, serial_number FROM products 
          WHERE (name LIKE '%$q%' OR barcode LIKE '%$q%' OR serial_number LIKE '%$q%') 
          AND stock_quantity > 0 
          LIMIT 10";

$result = $conn->query($query);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>
