<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Soft delete: keep the product in DB but hide it from management
    if ($conn->query("UPDATE products SET is_deleted = 1 WHERE id = $id")) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}
?>
