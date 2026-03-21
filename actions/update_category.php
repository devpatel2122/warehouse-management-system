<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_id = intval($_POST['old_cid'] ?? 0);
    $new_id = intval($_POST['new_cid'] ?? 0);
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');

    if ($old_id <= 0 || $new_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Category ID']);
        exit();
    }

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required.']);
        exit();
    }

    // Start Transaction to ensure data integrity
    $conn->begin_transaction();
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    try {
        // 1. If ID is being changed, perform checks and cascade
        if ($old_id !== $new_id) {
            // Check if new ID is already taken
            $check = $conn->query("SELECT id FROM categories WHERE id = $new_id");
            if ($check->num_rows > 0) {
                throw new Exception("The new ID ($new_id) is already in use by another category.");
            }

            // CASCADE: Update Products
            if(!$conn->query("UPDATE products SET category_id = $new_id WHERE category_id = $old_id")) {
                throw new Exception("Product update failed: " . $conn->error);
            }
            
            // CASCADE: Update Sub-Categories
            if(!$conn->query("UPDATE sub_categories SET category_id = $new_id WHERE category_id = $old_id")) {
                throw new Exception("Sub-category update failed: " . $conn->error);
            }

            // Update the Category ID itself
            $stmtId = $conn->prepare("UPDATE categories SET id = ? WHERE id = ?");
            $stmtId->bind_param("ii", $new_id, $old_id);
            $stmtId->execute();
            $stmtId->close();
            
            $active_id = $new_id;
        } else {
            $active_id = $old_id;
        }

        // 2. Update Name and Description
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $active_id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();

        $conn->commit();
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $conn->rollback();
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
