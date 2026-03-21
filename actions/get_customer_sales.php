<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode([]);
    exit();
}

$result = $conn->query("SELECT * FROM sales WHERE customer_id = $id ORDER BY id DESC");
$sales = [];
while ($row = $result->fetch_assoc()) {
    $sales[] = $row;
}

echo json_encode($sales);
?>
