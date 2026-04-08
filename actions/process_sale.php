<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$customer_id = !empty($input['customer_id']) ? intval($input['customer_id']) : null;
$items = $input['items'] ?? [];
$total_amount = floatval($input['total'] ?? 0);

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit();
}

// Start transaction
$conn->begin_transaction();

// Calculate GST Breakdown if configured
$cgst_rate = floatval($settings['cgst_rate'] ?? 0);
$sgst_rate = floatval($settings['sgst_rate'] ?? 0);

$base_amount = $total_amount / (1 + ($cgst_rate + $sgst_rate)/100);
$cgst_amount = ($base_amount * $cgst_rate) / 100;
$sgst_amount = ($base_amount * $sgst_rate) / 100;

try {
    // 1. Generate Custom Invoice Number
    $prefix = $settings['invoice_prefix'] ?? 'INV-';
    $next_no = intval($settings['next_invoice_no'] ?? 1);
    $invoice_no = $prefix . str_pad($next_no, 5, '0', STR_PAD_LEFT);
    $sale_date = !empty($input['sale_date']) ? $conn->real_escape_string($input['sale_date']) : date('Y-m-d');

    // 2. Insert into sales table with tax breakdown and payment info
    $payment_method = $input['payment_method'] ?? 'Cash';
    // Use manual status if provided, otherwise fallback to method defaults
    $payment_status = $input['payment_status'] ?? (($payment_method == 'Credit' || $payment_method == 'UPI') ? 'Unpaid' : 'Paid');
    $walkin_name = !empty($input['walkin_name']) ? $conn->real_escape_string($input['walkin_name']) : null;
    $walkin_contact = !empty($input['walkin_contact']) ? $conn->real_escape_string($input['walkin_contact']) : null;
    $walkin_address = !empty($input['walkin_address']) ? $conn->real_escape_string($input['walkin_address']) : null;
    $transaction_ref = !empty($input['transaction_ref']) ? $conn->real_escape_string($input['transaction_ref']) : null;

    $stmt = $conn->prepare("INSERT INTO sales (customer_id, total_amount, cgst_amount, sgst_amount, payment_method, payment_status, invoice_no, sale_date, walkin_name, walkin_contact, walkin_address, transaction_ref) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idddssssssss", $customer_id, $total_amount, $cgst_amount, $sgst_amount, $payment_method, $payment_status, $invoice_no, $sale_date, $walkin_name, $walkin_contact, $walkin_address, $transaction_ref);
    $stmt->execute();
    $sale_id = $conn->insert_id;

    // 3. Increment Invoice Number in Settings
    $new_next_no = $next_no + 1;
    $conn->query("UPDATE settings SET setting_value = '$new_next_no' WHERE setting_key = 'next_invoice_no'");
    $stmt->close();

    // 2. Insert items and update stock
    $stmtItem = $conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    $stmtUpdateStock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");

    foreach ($items as $item) {
        $pid = intval($item['id']);
        $qty = floatval($item['qty']);
        $price = floatval($item['price']);

        // Check stock before proceeding
        $checkStock = $conn->query("SELECT stock_quantity FROM products WHERE id = $pid")->fetch_assoc();
        if ($checkStock['stock_quantity'] < $qty) {
            throw new Exception("Insufficient stock for product: " . $item['name']);
        }

        $stmtItem->bind_param("iidd", $sale_id, $pid, $qty, $price);
        $stmtItem->execute();

        $stmtUpdateStock->bind_param("di", $qty, $pid);
        $stmtUpdateStock->execute();
    }

    $conn->commit();
    
    // Award Gamification XP
    $xp_to_add = intval($settings['xp_per_sale'] ?? 50);
    $user_id = $_SESSION['user_id'];
    $conn->query("UPDATE users SET xp = xp + $xp_to_add WHERE id = $user_id");

    logActivity("New Sale Finalized", "sales", $sale_id, "Total: " . CURRENCY_SYMBOL . $total_amount . " (+$xp_to_add XP)");
    echo json_encode(['success' => true, 'sale_id' => $sale_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
