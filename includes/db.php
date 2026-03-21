<?php
/**
 * Warehouse Pro - Core Database Configuration
 * Author: Developer
 * Version: 1.2.0
 */

$host = 'localhost';
$dbname = 'warehouse_db';
$username = 'root';
$password = '';

// Primary DB Connection
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("CRITICAL ERROR: Could not connect to database. " . $conn->connect_error);
}

// Ensure proper encoding for special characters
$conn->set_charset("utf8mb4");

// Initialize Auth Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load System Settings from DB
$settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM settings");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Define Core Constants from DB Settings
define('CURRENCY_SYMBOL', $settings['system_currency'] ?? '₹');
define('APP_NAME', $settings['company_name'] ?? 'Warehouse Pro');

/**
 * Global Currency Formatter
 */
function formatMoney($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

// System Logs
require_once 'logger.php';
// No closing tag to avoid whitespace issues
