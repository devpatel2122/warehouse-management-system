<?php
/**
 * API Endpoint: Get Revenue by Main Category
 */
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$query = "SELECT c.name as category_name, SUM(si.quantity * si.unit_price) as total_revenue
          FROM sale_items si
          JOIN products p ON si.product_id = p.id
          JOIN categories c ON p.category_id = c.id
          GROUP BY c.id
          ORDER BY total_revenue DESC";

$result = $conn->query($query);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'name' => $row['category_name'],
        'revenue' => (float)$row['total_revenue']
    ];
}

echo json_encode($data);
?>
