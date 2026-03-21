<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'inventory_dept') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $contact = $conn->real_escape_string($_POST['contact_person']);
    $phone = $conn->real_escape_string($_POST['phone']);

    if ($id > 0) {
        $q = "UPDATE warehouses SET name='$name', location='$location', contact_person='$contact', phone='$phone' WHERE id=$id";
    } else {
        $q = "INSERT INTO warehouses (name, location, contact_person, phone) VALUES ('$name', '$location', '$contact', '$phone')";
    }

    if ($conn->query($q)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}
?>
