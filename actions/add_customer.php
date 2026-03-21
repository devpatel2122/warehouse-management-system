<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');

    $id = intval($_POST['id'] ?? 0);

    if (empty($name) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Name and Phone are required.']);
        exit();
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE customers SET name=?, phone=?, email=?, address=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $phone, $email, $address, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $address);
    }

    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'message' => $conn->error]);
    $stmt->close();
}
?>
