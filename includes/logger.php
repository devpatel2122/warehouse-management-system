<?php
/**
 * Warehouse Pro - Internal Audit Logger
 */
function logActivity($action, $table = null, $id = null, $details = null) {
    global $conn;
    
    // Safety check session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) return; // Don't log anonymous actions if not needed

    $id = intval($id);
    
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, target_table, target_id, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issis", $user_id, $action, $table, $id, $details);
    $stmt->execute();
    $stmt->close();
}
?>
