<?php
require_once 'includes/db.php';
$q = 'Apple';
$query = "SELECT p.id, p.name, p.price, p.stock_quantity, p.barcode, p.serial_number, c.name as category_name 
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
          WHERE (p.name LIKE '%$q%' OR p.barcode LIKE '%$q%' OR p.serial_number LIKE '%$q%' OR c.name LIKE '%$q%' OR sc.name LIKE '%$q%') 
          AND p.is_deleted = 0 
          ORDER BY p.name ASC
          LIMIT 20";

$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo $conn->error;
}
?>
