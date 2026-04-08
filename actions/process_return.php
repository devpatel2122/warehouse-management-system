<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$invoice_no = trim($input['invoice_no'] ?? '');
$items = $input['items'] ?? [];

if (empty($invoice_no) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit();
}

$conn->begin_transaction();

try {
    foreach ($items as $item) {
        $sale_item_id = intval($item['sale_item_id']);
        $product_id = intval($item['product_id']);
        $qty = floatval($item['qty']);
        $reason = $conn->real_escape_string($item['reason']);
        $action = $conn->real_escape_string($item['action']); // 'restock' or 'discard'

        // 1. Record the return
        $stmt = $conn->prepare("INSERT INTO returns (invoice_no, sale_item_id, product_id, quantity, reason, action) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siidss", $invoice_no, $sale_item_id, $product_id, $qty, $reason, $action);
        $stmt->execute();

        // 2. If restock, update product stock
        if ($action === 'restock') {
            $conn->query("UPDATE products SET stock_quantity = stock_quantity + $qty WHERE id = $product_id");
        }
        
        // 3. Optional: update the sale details? 
        // For now, only recording the return record is sufficient for this feature.
    }

    $conn->commit();
    logActivity("Sales Return Processed", "returns", count($items), "Invoice: $invoice_no");
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
