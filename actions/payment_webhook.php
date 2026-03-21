<?php
/**
 * Warehouse Pro - Universal Payment Webhook
 * This file receives signals from external payment providers
 */
require_once '../includes/db.php';

// 1. Get the payload (This is what the payment app sends)
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// 2. Security Log (Check what was received)
file_put_contents('webhook_log.txt', date('[Y-m-d H:i:s] ') . $payload . PHP_EOL, FILE_APPEND);

// Razorpay usually sends data nested within an 'event' and 'payload'
$sale_id = null;
$ref = 'RAZORPAY-TEST';

if (isset($data['payload']['payment']['entity'])) {
    $entity = $data['payload']['payment']['entity'];
    // In your POS code, you should add the sale_id to 'notes' when creating the Razorpay order
    $sale_id = $entity['notes']['invoice_id'] ?? null;
    $ref = $entity['id'] ?? 'RAZORPAY-AUTO';
} elseif (isset($data['invoice_id'])) {
    // Fallback for our manual CURL test
    $sale_id = intval($data['invoice_id']);
    $ref = $data['transaction_id'] ?? 'MOCK-AUTO';
}

if ($sale_id) {
    // 3. Update the database
    $stmt = $conn->prepare("UPDATE sales SET payment_status = 'Paid', transaction_ref = ?, payment_method = 'UPI' WHERE id = ?");
    $stmt->bind_param("si", $ref, $sale_id);
    
    if ($stmt->execute()) {
        logActivity("External Payment Verified", "sales", $sale_id, "Verified via Razorpay/Webhook Callback");
        echo json_encode(['status' => 'success']);
    }
} else {
    echo json_encode(['status' => 'ignored', 'error' => 'No Sale ID found in payload']);
}
?>
