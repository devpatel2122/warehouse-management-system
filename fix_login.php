<?php
require_once 'includes/db.php';

$username = 'admin';
$password = 'admin123'; // Setting a simpler password to avoid confusion
$hashedToken = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Login Fix Utility</h2>";

// 1. Check if user exists
$check = $conn->query("SELECT id FROM users WHERE username = '$username'");

if ($check->num_rows > 0) {
    // 2. Update existing user
    $sql = "UPDATE users SET password = '$hashedToken' WHERE username = '$username'";
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Successfully updated user '<b>$username</b>' with password '<b>$password</b>'.</p>";
    } else {
        echo "<p style='color: red;'>Error updating: " . $conn->error . "</p>";
    }
} else {
    // 3. Create user if doesn't exist
    $sql = "INSERT INTO users (username, password, email, role) VALUES ('$username', '$hashedToken', 'admin@example.com', 'admin')";
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Successfully created user '<b>$username</b>' with password '<b>$password</b>'.</p>";
    } else {
        echo "<p style='color: red;'>Error creating: " . $conn->error . "</p>";
    }
}

echo "<hr><p>Please try logging in now at <a href='index.php'>index.php</a> using:</p>";
echo "<b>Username:</b> $username<br>";
echo "<b>Password:</b> $password<br>";
echo "<br><small>Delete this file (fix_login.php) after you login for security.</small>";
?>
