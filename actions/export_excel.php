<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

// Clear any previous output (like whitespace from includes)
if (ob_get_length()) ob_clean();

$type = $_GET['type'] ?? 'products';
$filename = $type . "_sheet_" . date('Y-m-d') . ".csv";

// Set headers to force CSV download (most compatible for "Excel" sheets)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '";');
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

// Open the output stream
$output = fopen('php://output', 'w');

// Output BOM for UTF-8 compatibility (especially for Excel/Numbers)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

if ($type === 'products') {
    // Header for Products: Category, Item Name, Price, Total Stock, Used Stock, Remaining Stock
    fputcsv($output, ['Category', 'Item Name', 'Price', 'Total Stock', 'Used Stock', 'Remaining Stock']);
    
    // Query to get products with their category name and sold quantity
    $query = "SELECT 
                c.name as category_name,
                p.name, 
                p.stock_quantity, 
                IFNULL((SELECT SUM(quantity) FROM sale_items WHERE product_id = p.id), 0) as used_stock,
                p.price 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE p.is_deleted = 0
              ORDER BY c.name ASC, p.name ASC";
              
    $res = $conn->query($query);

    while ($row = $res->fetch_assoc()) {
        $category = $row['category_name'] ?: 'Uncategorized';
        
        $used = floatval($row['used_stock']);
        $remaining = floatval($row['stock_quantity']);
        $total = $used + $remaining;

        fputcsv($output, [
            $category,
            $row['name'],
            $row['price'],
            $total,
            $used,
            $remaining
        ]);
    }

} elseif ($type === 'sales') {
    // Header for Sales
    fputcsv($output, ['Sale ID', 'Customer Name', 'Total Revenue', 'Transaction Date']);
    
    $query = "SELECT s.id, IFNULL(c.name, 'Walk-in'), s.total_amount, s.sale_date FROM sales s LEFT JOIN customers c ON s.customer_id = c.id ORDER BY s.id DESC";
    $res = $conn->query($query);
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit();
