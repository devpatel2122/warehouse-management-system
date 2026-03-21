<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$query = "SELECT s.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone 
          FROM sales s 
          LEFT JOIN customers c ON s.customer_id = c.id 
          ORDER BY s.id DESC";

$result = $conn->query($query);
$sales = [];
while ($row = $result->fetch_assoc()) {
    $sales[] = $row;
}

echo json_encode($sales);
?>
