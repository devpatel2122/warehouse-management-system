<?php
require_once '../includes/db.php';
require_once '../includes/emailer.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);
$email = $conn->real_escape_string($input['email'] ?? '');
$is_test = $input['is_test'] ?? false;

if (!$email && !$is_test) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$company_name = $settings['company_name'] ?? 'Warehouse Pro';

if ($is_test) {
    $subject = "Test Dispatch from $company_name";
    $message = "
    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #6366f1; border-radius: 10px; text-align: center;'>
        <h2 style='color: #6366f1;'>Engine Connection Success!</h2>
        <p>Your SMTP configurations are healthy and ready for enterprise dispatch.</p>
        <p style='color: #666; font-size: 13px;'>Sent at: " . date('Y-m-d H:i:s') . "</p>
    </div>";
} else {
    // Fetch sale details for the email content
    $sale = $conn->query("SELECT s.*, c.name as customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE s.id = $id")->fetch_assoc();
    $itemsRes = $conn->query("SELECT si.*, p.name as product_name FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = $id");

    if (!$sale) {
        echo json_encode(['success' => false, 'message' => 'Invoice not found']);
        exit();
    }

    $inv_no = "INV-" . str_pad($id, 5, '0', STR_PAD_LEFT);

    // Build Premium HTML Message
    $itemRows = "";
    while ($item = $itemsRes->fetch_assoc()) {
        $itemRows .= "<tr><td style='padding:10px; border-bottom:1px solid #ddd;'>{$item['product_name']}</td><td style='padding:10px; border-bottom:1px solid #ddd;'>{$item['quantity']}</td><td style='padding:10px; border-bottom:1px solid #ddd; text-align:right;'>".CURRENCY_SYMBOL.number_format($item['unit_price'], 2)."</td></tr>";
    }

    $message = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
        <div style='text-align: center; margin-bottom: 20px;'>
            <h2 style='color: #6366f1; margin: 0;'>$company_name</h2>
            <p style='color: #666;'>Tax Invoice / Bill of Supply</p>
        </div>
        <div style='margin-bottom: 20px;'>
            <p><strong>To:</strong> {$sale['customer_name']}</p>
            <p><strong>Invoice No:</strong> $inv_no</p>
            <p><strong>Date:</strong> {$sale['sale_date']}</p>
        </div>
        <table style='width: 100%; border-collapse: collapse;'>
            <thead>
                <tr style='background: #f8fafc;'>
                    <th style='padding: 10px; text-align: left;'>Item</th>
                    <th style='padding: 10px; text-align: left;'>Qty</th>
                    <th style='padding: 10px; text-align: right;'>Price</th>
                </tr>
            </thead>
            <tbody>
                $itemRows
            </tbody>
        </table>
        <div style='margin-top: 20px; text-align: right;'>
            <p style='font-size: 18px; color: #6366f1;'><strong>Grand Total: ".CURRENCY_SYMBOL.number_format($sale['total_amount'], 2)."</strong></p>
        </div>
        <hr style='border: 0; border-top: 1px solid #eee; margin: 30px 0;'>
        <p style='color: #999; font-size: 12px; text-align: center;'>This is a computer-generated invoice. No signature required.</p>
    </div>";

    $subject = "Invoice #$inv_no from $company_name";
}

try {
    $emailer = new EnterpriseEmailer($settings);
    $result = $emailer->send($email, $subject, $message);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'SMTP server accepted connection but failed to send.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Emailing failed: ' . $e->getMessage()]);
}
?>
