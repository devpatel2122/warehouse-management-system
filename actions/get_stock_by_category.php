<?php
/**
 * API Endpoint: Get Live Stock by Main Category
 * Returns warehouse inventory grouped by main product categories only
 */
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Query to get stock quantity by category
    $query = "
        SELECT 
            c.name as category_name,
            c.id as category_id,
            SUM(p.stock_quantity) as total_stock,
            COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        GROUP BY c.id, c.name
        HAVING total_stock > 0
        ORDER BY total_stock DESC
    ";
    
    $result = $conn->query($query);
    
    $categories = [];
    $colors = [
        '#f59e0b', // Orange - Electronics
        '#3b82f6', // Blue - Furniture
        '#10b981', // Green - Stationery
        '#8b5cf6', // Purple - Tools
        '#ef4444', // Red - Safety Gear
        '#ec4899', // Pink - Others
        '#14b8a6', // Teal
        '#f97316', // Orange Red
    ];
    
    $colorIndex = 0;
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'name' => $row['category_name'],
            'stock' => (int)$row['total_stock'],
            'products' => (int)$row['product_count'],
            'color' => $colors[$colorIndex % count($colors)]
        ];
        $colorIndex++;
    }
    
    echo json_encode($categories);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
