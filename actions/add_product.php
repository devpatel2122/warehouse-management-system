<?php
/**
 * Product Data Controller - Handles Create & Update
 */
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $sub_category_id = !empty($_POST['sub_category_id']) ? intval($_POST['sub_category_id']) : null;
    $serial_number = !empty($_POST['serial_number']) ? $conn->real_escape_string($_POST['serial_number']) : null;
    $barcode = !empty($_POST['barcode']) ? $conn->real_escape_string($_POST['barcode']) : null;
    $price = floatval($_POST['price'] ?? 0);
    $purchase_price = floatval($_POST['purchase_price'] ?? 0);
    $stock_quantity = floatval($_POST['stock_quantity'] ?? 0);
    $reorder_level = intval($_POST['reorder_level'] ?? 5);
    $batch_number = $conn->real_escape_string($_POST['batch_number'] ?? '');
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
    $hsn_code = $conn->real_escape_string($_POST['hsn_code'] ?? '');
    $warehouse_id = intval($_POST['warehouse_id'] ?? 1);
    $description = $conn->real_escape_string($_POST['description'] ?? '');

    $rack_location = $conn->real_escape_string($_POST['rack_location'] ?? 'A1');
    $bin_location = $conn->real_escape_string($_POST['bin_location'] ?? '001');

    // ... (rest of the code for file upload)
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $target = '../uploads/products/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_path = 'uploads/products/' . $filename;
        }
    }

    $id = intval($_POST['id'] ?? 0);

    if (empty($name) || $category_id == 0) {
        echo json_encode(['success' => false, 'message' => 'Product name and category are mandatory']);
        exit();
    }

    if ($id > 0) {
        $sql = "UPDATE products SET name=?, category_id=?, sub_category_id=?, serial_number=?, barcode=?, price=?, purchase_price=?, stock_quantity=?, reorder_level=?, batch_number=?, expiry_date=?, hsn_code=?, warehouse_id=?, description=?, rack_location=?, bin_location=?";
        $params = [$name, $category_id, $sub_category_id, $serial_number, $barcode, $price, $purchase_price, $stock_quantity, $reorder_level, $batch_number, $expiry_date, $hsn_code, $warehouse_id, $description, $rack_location, $bin_location];
        $types = "siissdddisssisss"; 
        
        // Corrected types string with sub_category_id:
        // 1s(name) 2i(cat) 3i(sub_cat) 4s(serial) 5s(barcode) 6d(price) 7d(purchase) 8d(stock) 9i(reorder) 10s(batch) 11s(expiry) 12s(hsn) 13i(warehouse) 14s(desc) 15s(rack) 16s(bin)
        $types = "siissdddisssisss"; 

        if ($image_path) {
            $sql .= ", image_path=?";
            $params[] = $image_path;
            $types .= "s";
        }

        $sql .= " WHERE id=?";
        $params[] = $id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, category_id, sub_category_id, serial_number, barcode, price, purchase_price, stock_quantity, reorder_level, batch_number, expiry_date, hsn_code, warehouse_id, description, rack_location, bin_location, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siissdddisssissss", $name, $category_id, $sub_category_id, $serial_number, $barcode, $price, $purchase_price, $stock_quantity, $reorder_level, $batch_number, $expiry_date, $hsn_code, $warehouse_id, $description, $rack_location, $bin_location, $image_path);
    }

    if ($stmt->execute()) {
        $logId = ($id > 0) ? $id : $conn->insert_id;
        $logAction = ($id > 0) ? "Product Updated" : "New Product Added";
        
        // Award XP only for NEW products
        if ($id == 0) {
            $xp_reward = intval($settings['xp_per_product'] ?? 10);
            $uid = $_SESSION['user_id'];
            $conn->query("UPDATE users SET xp = xp + $xp_reward WHERE id = $uid");
            $logAction .= " (+$xp_reward XP)";
        }

        logActivity($logAction, "products", $logId, "$name (Stock: $stock_quantity)");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Execution failed: ' . $conn->error]);
    }
    $stmt->close();
}
?>
