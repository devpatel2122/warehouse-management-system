<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'update_info') {
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $two_fa_enabled = isset($_POST['2fa_enabled']) ? 1 : 0;
    
    // Handle Avatar Upload
    $avatar_path = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename = "avatar_" . $user_id . "_" . time() . "." . $ext;
        $target = "../uploads/avatars/" . $filename;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            $avatar_path = "uploads/avatars/" . $filename;
        }
    }

    if ($avatar_path) {
        $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, 2fa_enabled = ?, avatar_path = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $email, $phone, $two_fa_enabled, $avatar_path, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, 2fa_enabled = ? WHERE id = ?");
        $stmt->bind_param("ssii", $email, $phone, $two_fa_enabled, $user_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
    }
    $stmt->close();

} elseif ($action === 'update_password') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($current, $user['password'])) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->bind_param("si", $hashed, $user_id);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
        $updateStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Current password incorrect']);
    }
    $stmt->close();
}
?>
