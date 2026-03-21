<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$result = $conn->query("SELECT * FROM vendors ORDER BY name ASC");
$vendors = [];
while ($row = $result->fetch_assoc()) {
    $vendors[] = $row;
}

echo json_encode($vendors);
?>
