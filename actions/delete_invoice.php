<?php
/**
 * Action: Delete Invoice
 * Security: Admin Only
 */
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// RESTRICT TO ADMIN
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Only administrators can delete invoices.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$sale_id = intval($input['id'] ?? 0);

if (!$sale_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid Invoice ID']);
    exit();
}

$conn->begin_transaction();

try {
    // 1. Get items to restock
    $items = $conn->query("SELECT product_id, quantity FROM sale_items WHERE sale_id = $sale_id");
    
    // 2. Restock products
    while ($item = $items->fetch_assoc()) {
        $pid = $item['product_id'];
        $qty = $item['quantity'];
        $conn->query("UPDATE products SET stock_quantity = stock_quantity + $qty WHERE id = $pid");
    }

    // 3. Delete from sale_items
    $conn->query("DELETE FROM sale_items WHERE sale_id = $sale_id");

    // 4. Delete from sales
    $stmt = $conn->prepare("DELETE FROM sales WHERE id = ?");
    $stmt->bind_param("i", $sale_id);
    
    if ($stmt->execute()) {
        logActivity("Invoice Deleted and Stock Reverted", "sales", $sale_id, "System Record Removed");
        $conn->commit();
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Failed to delete invoice record.");
    }

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
