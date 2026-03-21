<?php
/**
 * Script to distribute stock equally across all categories for testing
 */
require_once 'includes/db.php';

// Get all categories
$categories_query = "SELECT id, name FROM categories ORDER BY id";
$categories_result = $conn->query($categories_query);

$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

$total_categories = count($categories);
if ($total_categories == 0) {
    die("No categories found!");
}

// Set equal stock for each category (100 units per product)
$stock_per_product = 100;

echo "Distributing stock equally across categories...\n\n";

foreach ($categories as $category) {
    // Get products in this category
    $products_query = "SELECT id, name FROM products WHERE category_id = " . $category['id'];
    $products_result = $conn->query($products_query);
    
    $product_count = 0;
    while ($product = $products_result->fetch_assoc()) {
        // Update stock to equal amount
        $update_query = "UPDATE products SET stock_quantity = $stock_per_product WHERE id = " . $product['id'];
        $conn->query($update_query);
        $product_count++;
    }
    
    $total_stock = $product_count * $stock_per_product;
    echo "✓ {$category['name']}: {$product_count} products × {$stock_per_product} units = {$total_stock} total\n";
}

echo "\n✅ Stock distribution completed successfully!\n";
echo "Refresh your dashboard to see the balanced donut chart.\n";
?>
