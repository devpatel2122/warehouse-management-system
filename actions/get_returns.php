<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$res = $conn->query("SELECT r.*, p.name as product_name 
                      FROM returns r 
                      JOIN products p ON r.product_id = p.id 
                      ORDER BY r.return_date DESC");

$returns = [];
while ($row = $res->fetch_assoc()) {
    $returns[] = $row;
}

echo json_encode([
    'success' => true,
    'returns' => $returns
]);
?>
