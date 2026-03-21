<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

$type = $_GET['type'] ?? 'products';
$filename = $type . "_export_" . date('Y-m-d') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

$output = fopen('php://output', 'w');

if ($type === 'products') {
    fputcsv($output, ['ID', 'Name', 'Barcode', 'Serial Number', 'Stock', 'Buy Price', 'Sell Price']);
    $query = "SELECT id, name, barcode, serial_number, stock_quantity, purchase_price, price FROM products ORDER BY name ASC";
    $res = $conn->query($query);
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, $row);
    }
} elseif ($type === 'sales') {
    fputcsv($output, ['ID', 'Customer', 'Total Amount', 'Date']);
    $query = "SELECT s.id, c.name, s.total_amount, s.sale_date FROM sales s LEFT JOIN customers c ON s.customer_id = c.id ORDER BY s.id DESC";
    $res = $conn->query($query);
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, $row);
    }
} elseif ($type === 'purchases') {
    fputcsv($output, ['ID', 'Vendor', 'Total Amount', 'Date']);
    $query = "SELECT p.id, v.name, p.total_amount, p.purchase_date FROM purchases p LEFT JOIN vendors v ON p.vendor_id = v.id ORDER BY p.id DESC";
    $res = $conn->query($query);
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit();
?>
