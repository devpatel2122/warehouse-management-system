<?php
/**
 * Warehouse Pro - Payment Status Updater
 */
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Ensure we have a valid input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
    exit();
}

$id = intval($input['id'] ?? 0);
$ref = isset($input['ref']) && !empty($input['ref']) ? trim($conn->real_escape_string($input['ref'])) : 'MANUAL';

// Basic validation
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Invoice ID.']);
    exit();
}

// Check if sales exists first? (Optional but good)
$stmt = $conn->prepare("UPDATE sales SET payment_status = 'Paid', transaction_ref = ? WHERE id = ?");
$stmt->bind_param("si", $ref, $id);

if ($stmt->execute()) {
    // If it was previously Unpaid, this is a significant event to log
    logActivity("Invoice Marked Paid", "sales", $id, "Ref: $ref");
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}
?>
