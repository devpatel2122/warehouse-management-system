<?php
/**
 * Warehouse Pro - CSV Processor
 */
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') exit();

if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, "r");
    
    // Skip Header
    fgetcsv($handle);
    
    $imported = 0;
    $conn->begin_transaction();
    
    try {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $name = $conn->real_escape_string($data[0]);
            $cat_id = intval($data[1]);
            $sku = $conn->real_escape_string($data[2]);
            $price = floatval($data[3]);
            $stock = intval($data[4]);
            
            if (empty($name)) continue;

            $stmt = $conn->prepare("INSERT INTO products (name, category_id, barcode, price, stock_quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sisdi", $name, $cat_id, $sku, $price, $stock);
            $stmt->execute();
            $imported++;
        }
        $conn->commit();
        logActivity("Bulk Import Completed", "products", 0, "$imported products added via CSV");
        echo json_encode(['success' => true, 'imported' => $imported]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    fclose($handle);
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
}
?>
