<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$invoice_no = trim($_GET['invoice_no'] ?? '');

if (empty($invoice_no)) {
    echo json_encode(['success' => false, 'message' => 'Invoice number is required']);
    exit();
}

// 1. Get sale header
$stmt = $conn->prepare("SELECT s.*, c.name as customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE s.invoice_no = ?");
$stmt->bind_param("s", $invoice_no);
$stmt->execute();
$sale = $stmt->get_result()->fetch_assoc();

if (!$sale) {
    echo json_encode(['success' => false, 'message' => 'Invoice not found']);
    exit();
}

$sale_id = $sale['id'];

// 2. Get items
$itemsRes = $conn->query("SELECT si.*, p.name as product_name 
                          FROM sale_items si 
                          JOIN products p ON si.product_id = p.id 
                          WHERE si.sale_id = $sale_id");

$items = [];
while ($row = $itemsRes->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode([
    'success' => true,
    'sale' => $sale,
    'items' => $items
]);
?>
