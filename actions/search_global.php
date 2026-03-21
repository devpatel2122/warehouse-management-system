<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$q = isset($_GET['q']) ? $_GET['q'] : '';
$qArr = '%' . $q . '%';

$results = [];

// 1. Search Products
$stmt = $conn->prepare("SELECT id, name, barcode FROM products WHERE name LIKE ? OR barcode LIKE ? LIMIT 5");
$stmt->bind_param("ss", $qArr, $qArr);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $results['Products'][] = [
        'title' => $row['name'],
        'subtitle' => "Barcode: " . ($row['barcode'] ?? 'N/A'),
        'link' => "/warehouse/modules/product/inventory.php"
    ];
}

// 2. Search Customers
$stmt = $conn->prepare("SELECT id, name, phone FROM customers WHERE name LIKE ? OR phone LIKE ? LIMIT 5");
$stmt->bind_param("ss", $qArr, $qArr);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $results['Customers'][] = [
        'title' => $row['name'],
        'subtitle' => "Phone: " . $row['phone'],
        'link' => "/warehouse/modules/sell/customers.php"
    ];
}

// 3. Search Sales (Invoices)
$stmt = $conn->prepare("SELECT s.id, s.total_amount, c.name as customer_name FROM sales s 
                        LEFT JOIN customers c ON s.customer_id = c.id
                        WHERE s.id LIKE ? OR c.name LIKE ? LIMIT 5");
$stmt->bind_param("ss", $qArr, $qArr);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $results['Invoices'][] = [
        'title' => "Invoice #" . $row['id'],
        'subtitle' => "Customer: " . ($row['customer_name'] ?? 'Walk-in') . " - Total: " . CURRENCY_SYMBOL . $row['total_amount'],
        'link' => "/warehouse/modules/sell/invoices.php"
    ];
}

echo json_encode($results);
