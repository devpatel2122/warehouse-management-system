<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'product_dept') {
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
    exit();
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    if ($conn->query("DELETE FROM categories WHERE id = $id")) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}
?>
