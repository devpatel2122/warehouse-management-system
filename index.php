<?php
require_once 'includes/db.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Stock Inventory System</title>
    <meta name="description" content="Secure login for Warehouse Stock Inventory System">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <!-- Using original branding as requested -->
                <h1>Warehouse Pro</h1>
                <p>Sign in to your account</p>
            </div>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required placeholder="Enter username">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="Enter password">
                </div>
                
                <div id="loginMessage" class="mb-4 text-center" style="display: none; font-size: 14px;"></div>
                
                <button type="submit" class="btn">
                    Sign In
                </button>
            </form>

            <form id="otpForm" style="display: none;">
                <div class="auth-header" style="margin-bottom: 20px;">
                    <h2>Two-Factor Auth</h2>
                    <p>Enter the 6-digit code sent to your email</p>
                </div>
                
                <div class="form-group">
                    <label for="otp">Security Code</label>
                    <input type="text" id="otp" name="otp" class="form-input" required placeholder="000000" maxlength="6" pattern="\d{6}">
                </div>
                
                <div id="otpMessage" class="mb-4 text-center" style="display: none; font-size: 14px;"></div>
                
                <button type="submit" class="btn">
                    Verify Code
                </button>
                <div class="text-center mt-4">
                    <a href="#" id="backToLogin" class="text-sm">Back to Login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/auth.js"></script>
</body>
</html>
