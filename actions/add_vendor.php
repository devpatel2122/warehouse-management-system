<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $contact = $conn->real_escape_string($_POST['contact_person'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');

    $id = intval($_POST['id'] ?? 0);

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Company name is required.']);
        exit();
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE vendors SET name=?, contact_person=?, phone=?, email=?, address=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $contact, $phone, $email, $address, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO vendors (name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $contact, $phone, $email, $address);
    }

    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'message' => $conn->error]);
    $stmt->close();
}
?>
