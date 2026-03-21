<?php
/**
 * Verify OTP - Part 2 of 2FA login
 */
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Direct access not allowed']);
    exit();
}

$otp = $_POST['otp'] ?? '';

if (empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'OTP is required']);
    exit();
}

// Check if there's a pending login
if (!isset($_SESSION['pending_user_id']) || !isset($_SESSION['pending_otp'])) {
    echo json_encode(['success' => false, 'message' => 'No pending login found. Please sign in again.']);
    exit();
}

// Check expiry
if (time() > $_SESSION['pending_otp_expiry']) {
    unset($_SESSION['pending_user_id']);
    unset($_SESSION['pending_otp']);
    unset($_SESSION['pending_otp_expiry']);
    echo json_encode(['success' => false, 'message' => 'OTP expired. Please try again.']);
    exit();
}

// Verify OTP
if ($otp === $_SESSION['pending_otp']) {
    // Correct OTP - Finalize Login
    $user_id = $_SESSION['pending_user_id'];
    
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Log activity
        logActivity("Secure 2FA Login", "users", $user['id'], "User verified via SMS OTP");
        
        // Clear pending data
        unset($_SESSION['pending_user_id']);
        unset($_SESSION['pending_otp']);
        unset($_SESSION['pending_otp_expiry']);
        
        echo json_encode(['success' => true, 'message' => 'Verified successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect OTP code']);
}
?>
