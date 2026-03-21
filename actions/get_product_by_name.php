<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$name = $_GET['name'] ?? '';
if (empty($name)) {
    echo json_encode(['success' => false]);
    exit();
}

$name = $conn->real_escape_string($name);
$query = "SELECT * FROM products WHERE name = '$name' AND is_deleted = 0 ORDER BY id DESC LIMIT 1";
$result = $conn->query($query);

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'product' => $row]);
} else {
    echo json_encode(['success' => false]);
}
