<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = !empty($_POST['id']) ? intval($_POST['id']) : null;
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required.']);
        exit();
    }

    if ($id) {
        // Check if ID already exists
        $check = $conn->query("SELECT id FROM categories WHERE id = $id");
        if ($check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Category ID ' . $id . ' is already in use.']);
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO categories (id, name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id, $name, $description);
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    $stmt->close();
}
?>
