<?php
/**
 * Warehouse Pro - Analytics Engine
 * Provides data for Chart.js
 */
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) exit();

// 1. Sales Trend (Last 7 Days)
$sales_trend = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $res = $conn->query("SELECT SUM(total_amount) as daily_total FROM sales WHERE sale_date = '$date'");
    $row = $res->fetch_assoc();
    $sales_trend[] = [
        'day' => date('D', strtotime($date)),
        'total' => (float)($row['daily_total'] ?? 0)
    ];
}

// 2. Category Distribution
$categories = [];
$res = $conn->query("SELECT c.name, COUNT(p.id) as count 
                    FROM categories c 
                    LEFT JOIN products p ON c.id = p.category_id 
                    GROUP BY c.id");
if ($res) {
    while($row = $res->fetch_assoc()) {
        $categories[] = $row;
    }
}

// 3. Purchase vs Sales (Current Month & Year)
$currentMonth = date('m');
$currentYear = date('Y');

$res_s = $conn->query("SELECT SUM(total_amount) FROM sales WHERE MONTH(sale_date) = '$currentMonth' AND YEAR(sale_date) = '$currentYear'");
$total_sales = ($res_s && $row = $res_s->fetch_row()) ? ($row[0] ?? 0) : 0;

$res_p = $conn->query("SELECT SUM(total_amount) FROM purchases WHERE MONTH(purchase_date) = '$currentMonth' AND YEAR(purchase_date) = '$currentYear'");
$total_purchases = ($res_p && $row = $res_p->fetch_row()) ? ($row[0] ?? 0) : 0;

// Clean Output
if (ob_get_length()) ob_clean();
echo json_encode([
    'sales_trend' => $sales_trend,
    'categories' => $categories,
    'financials' => [
        'sales' => (float)$total_sales,
        'purchases' => (float)$total_purchases
    ]
]);
?>
