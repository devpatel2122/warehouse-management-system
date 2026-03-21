<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        foreach ($_POST as $key => $value) {
            // Use INSERT ... ON DUPLICATE KEY UPDATE to handle both create and update safely
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                                  ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $v = (string)$value;
            $stmt->bind_param("ss", $key, $v);
            $stmt->execute();
            $stmt->close();
        }
        $conn->commit();
        logActivity("Global Settings Updated", "settings", 0, "Various system keys updated by Admin");
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
