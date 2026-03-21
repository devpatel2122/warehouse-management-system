<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) exit();

// Logic: Look at sales for the last 14 days, calculate daily average, then see how long current stock lasts
$forecast = [];
$res = $conn->query("SELECT p.id, p.name, p.stock_quantity, 
                    (SELECT SUM(si.quantity) FROM sale_items si JOIN sales s ON si.sale_id = s.id WHERE si.product_id = p.id AND s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)) as total_sold
                    FROM products p 
                    WHERE p.stock_quantity > 0");

while($row = $res->fetch_assoc()) {
    if (!$row['total_sold']) continue;
    
    $avg_daily = $row['total_sold'] / 14;
    $days_left = floor($row['stock_quantity'] / ($avg_daily > 0 ? $avg_daily : 1));
    
    if ($days_left <= 7) { // Only show critical items (running out in a week)
        $forecast[] = [
            'name' => $row['name'],
            'stock' => $row['stock_quantity'],
            'days_left' => $days_left,
            'status' => ($days_left <= 2) ? 'CRITICAL' : 'WARNING'
        ];
    }
}

// Sort by severity
usort($forecast, function($a, $b) { return $a['days_left'] <=> $b['days_left']; });

echo json_encode(array_slice($forecast, 0, 5));
?>
