<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$where = "p.is_deleted = 0";
if ($q) {
    $where .= " AND (p.name LIKE '%$q%' OR p.barcode LIKE '%$q%' OR p.serial_number LIKE '%$q%')";
}

$query = "SELECT p.*, c.name as category_name, s.name as sub_category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN sub_categories s ON p.sub_category_id = s.id
          WHERE $where
          ORDER BY p.id DESC";

$result = $conn->query($query);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>
