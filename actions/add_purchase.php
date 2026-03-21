<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = intval($_POST['vendor_id'] ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = floatval($_POST['quantity'] ?? 0);
    $unit_price = floatval($_POST['unit_price'] ?? 0);
    $total_amount = $quantity * $unit_price;

    if ($product_id == 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
        exit();
    }

    $conn->begin_transaction();

    try {
        // 1. Insert into purchases
        $stmt = $conn->prepare("INSERT INTO purchases (vendor_id, total_amount, purchase_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("id", $vendor_id, $total_amount);
        $stmt->execute();
        $purchase_id = $conn->insert_id;
        $stmt->close();

        // 2. Insert into purchase_items
        $stmtItem = $conn->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $stmtItem->bind_param("iidd", $purchase_id, $product_id, $quantity, $unit_price);
        $stmtItem->execute();
        $stmtItem->close();

        // 3. Update product stock
        $stmtUpdate = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        $stmtUpdate->bind_param("di", $quantity, $product_id);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
