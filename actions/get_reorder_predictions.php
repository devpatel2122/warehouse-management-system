<?php
/**
 * Warehouse Pro - Smart Prediction Engine
 * Analyzes velocity and predicts stock depletion
 */
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) exit();

// Velocity = Sales in last 30 days / 30
$predictions = [];
$res = $conn->query("SELECT p.id, p.name, p.stock_quantity, p.reorder_level,
                    (SELECT SUM(si.quantity) FROM sale_items si JOIN sales s ON si.sale_id = s.id WHERE si.product_id = p.id AND s.sale_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as monthly_sales
                    FROM products p
                    WHERE p.stock_quantity > 0");

while ($row = $res->fetch_assoc()) {
    $monthly_sales = $row['monthly_sales'] ?? 0;
    $daily_velocity = $monthly_sales / 30;
    
    if ($daily_velocity > 0) {
        $days_left = floor($row['stock_quantity'] / $daily_velocity);
    } else {
        $days_left = 999; // Sufficient stock or no sales
    }

    // Only include if stock is under threshold or predicted to run out in < 15 days
    if ($row['stock_quantity'] <= $row['reorder_level'] || $days_left < 15) {
        $predictions[] = [
            'name' => $row['name'],
            'stock' => $row['stock_quantity'],
            'days_left' => $days_left,
            'status' => ($days_left < 7) ? 'CRITICAL' : 'WARNING'
        ];
    }
}

// Sort by days left (most urgent first)
usort($predictions, function($a, $b) {
    return $a['days_left'] - $b['days_left'];
});

echo json_encode(array_slice($predictions, 0, 5));
?>
