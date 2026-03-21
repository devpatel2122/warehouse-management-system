<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $cid = intval($_POST['category_id']);
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name is required.']);
        exit();
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE sub_categories SET category_id = ?, name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("issi", $cid, $name, $desc, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO sub_categories (category_id, name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $cid, $name, $desc);
    }

    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'message' => $conn->error]);
    $stmt->close();
}
?>
