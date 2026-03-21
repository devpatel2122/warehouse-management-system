<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$query = "SELECT p.*, v.name as vendor_name 
          FROM purchases p 
          LEFT JOIN vendors v ON p.vendor_id = v.id 
          ORDER BY p.id DESC";

$result = $conn->query($query);
$purchases = [];
while ($row = $result->fetch_assoc()) {
    $purchases[] = $row;
}

echo json_encode($purchases);
?>
