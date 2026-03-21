<?php
/**
 * Auth Controller - AJAX Login Processing
 */
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Direct access not allowed']);
    exit();
}

$username = $conn->real_escape_string($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Credentials required']);
    exit();
}

// Check database for user
$stmt = $conn->prepare("SELECT id, username, password, role, email, phone, 2fa_enabled FROM users WHERE username = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'System error (Check DB)']);
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Verify hashed password
    if (password_verify($password, $user['password'])) {
        // Check if 2FA is enabled
        if (isset($user['2fa_enabled']) && $user['2fa_enabled'] == 1 && !empty($user['phone'])) {
            // Generate OTP
            $otp = sprintf("%06d", mt_rand(1, 999999));
            
            // Store details in session for verification state
            $_SESSION['pending_user_id'] = $user['id'];
            $_SESSION['pending_otp'] = $otp;
            $_SESSION['pending_otp_expiry'] = time() + 300; // 5 minutes expiry
            
            // Send Email OTP
            require_once '../includes/emailer.php';
            try {
                $emailer = new EnterpriseEmailer($settings);
                $subject = "Security Code: " . $otp;
                $message = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #6366f1; border-radius: 10px; max-width: 400px; margin: 0 auto;'>
                    <h2 style='color: #6366f1; text-align: center;'>Warehouse Pro</h2>
                    <p>Hello <strong>" . $user['username'] . "</strong>,</p>
                    <p>Your two-factor authentication code is:</p>
                    <div style='background: #f1f5f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #1e293b; border-radius: 8px;'>
                        " . $otp . "
                    </div>
                    <p style='color: #64748b; font-size: 13px; margin-top: 20px;'>This code will expire in 5 minutes. If you did not request this, please secure your account.</p>
                </div>";
                
                $sent = $emailer->send($user['email'], $subject, $message);
                
                if ($sent) {
                    echo json_encode([
                        'success' => true, 
                        'requires_2fa' => true, 
                        'message' => 'Security code sent to your email'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Failed to send verification email. Check SMTP settings.'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Emailing Error: ' . $e->getMessage()
                ]);
            }
        } else {
            // Standard login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            echo json_encode(['success' => true, 'requires_2fa' => false]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User account not found']);
}

$stmt->close();
?>
