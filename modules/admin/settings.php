<?php
/**
 * Warehouse Pro - Enterprise Settings
 * Role: Admin Only
 */
$page_title = 'System Settings';
require_once '../../includes/db.php';
$base_path = '../../';

if ($_SESSION['role'] != 'admin') {
    header('Location: ' . $base_path . 'dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Warehouse System</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <?php include $base_path . 'includes/sidebar.php'; ?>
        <?php include $base_path . 'includes/top_nav.php'; ?>

        <main class="main-content">
            <header class="mb-4">
                <h1 style="font-size: 24px; font-weight: 700;">Control Center</h1>
                <p style="color: var(--text-muted);">Manage global configurations and business identity.</p>
            </header>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
                <!-- Identity Form -->
                <div style="background: var(--card-bg); padding: 32px; border-radius: 24px; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 24px;"><i class="fas fa-building" style="color: var(--primary);"></i> Business Identity</h3>
                    <form id="settingsForm">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Company Name</label>
                                <input type="text" name="company_name" class="form-input" value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Currency Symbol</label>
                                <select name="system_currency" class="form-input">
                                    <option value="₹" <?php echo ($settings['system_currency'] == '₹') ? 'selected' : ''; ?>>₹ (INR)</option>
                                    <option value="$" <?php echo ($settings['system_currency'] == '$') ? 'selected' : ''; ?>>$ (USD)</option>
                                    <option value="£" <?php echo ($settings['system_currency'] == '£') ? 'selected' : ''; ?>>£ (GBP)</option>
                                    <option value="€" <?php echo ($settings['system_currency'] == '€') ? 'selected' : ''; ?>>€ (EUR)</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Invoice Prefix</label>
                                <input type="text" name="invoice_prefix" class="form-input" value="<?php echo htmlspecialchars($settings['invoice_prefix'] ?? 'INV-'); ?>" placeholder="INV-">
                            </div>
                            <div class="form-group">
                                <label>Next Invoice No.</label>
                                <input type="number" name="next_invoice_no" class="form-input" value="<?php echo htmlspecialchars($settings['next_invoice_no'] ?? '1'); ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Business Email</label>
                                <input type="email" name="company_email" class="form-input" value="<?php echo htmlspecialchars($settings['company_email'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Primary Contact</label>
                                <input type="text" name="company_phone" class="form-input" value="<?php echo htmlspecialchars($settings['company_phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 30px;">
                            <label>Headquarters Address</label>
                            <textarea name="company_address" class="form-input" style="height: 80px;"><?php echo htmlspecialchars($settings['company_address'] ?? ''); ?></textarea>
                        </div>

                        <h3 style="margin: 32px 0 24px;"><i class="fas fa-file-invoice-dollar" style="color: var(--success);"></i> GST & Taxation (India)</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Company GSTIN</label>
                                <input type="text" name="company_gstin" class="form-input" value="<?php echo htmlspecialchars($settings['company_gstin'] ?? ''); ?>" placeholder="27AAACP1234A1Z5">
                            </div>
                            <div class="form-group">
                                <label>CGST Rate (%)</label>
                                <input type="number" step="0.1" name="cgst_rate" class="form-input" value="<?php echo htmlspecialchars($settings['cgst_rate'] ?? '9'); ?>">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>SGST Rate (%)</label>
                                <input type="number" step="0.1" name="sgst_rate" class="form-input" value="<?php echo htmlspecialchars($settings['sgst_rate'] ?? '9'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Merchant UPI ID (GPay/Paytm)</label>
                                <input type="text" name="merchant_upi" class="form-input" value="<?php echo htmlspecialchars($settings['merchant_upi'] ?? 'merchant@upi'); ?>" placeholder="yourname@upi">
                            </div>
                        </div>

                        <h3 style="margin: 32px 0 24px;"><i class="fas fa-plug" style="color: var(--primary);"></i> Payment Gateway (Automated)</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Razorpay Key ID (Test/Live)</label>
                                <input type="text" name="rzp_key_id" class="form-input" value="<?php echo htmlspecialchars($settings['rzp_key_id'] ?? ''); ?>" placeholder="rzp_test_...">
                            </div>
                            <div class="form-group">
                                <label>Razorpay Secret</label>
                                <input type="password" name="rzp_key_secret" class="form-input" value="<?php echo htmlspecialchars($settings['rzp_key_secret'] ?? ''); ?>" placeholder="Secret Key">
                            </div>
                        </div>

                        <h3 style="margin: 32px 0 24px;"><i class="fas fa-envelope-open-text" style="color: #f59e0b;"></i> Email Engine (SMTP)</h3>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" name="smtp_host" class="form-input" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>" placeholder="smtp.gmail.com">
                            </div>
                            <div class="form-group">
                                <label>Port</label>
                                <input type="number" name="smtp_port" class="form-input" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                            <div class="form-group">
                                <label>Email / Username</label>
                                <input type="text" name="smtp_user" class="form-input" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Password / App Key</label>
                                <input type="password" name="smtp_pass" class="form-input" value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>">
                            </div>
                        </div>

                        <div style="display: flex; gap: 15px;">
                            <button type="submit" class="btn" id="saveSetBtn">Save Global Settings</button>
                            <button type="button" class="btn" style="background: var(--secondary); width: auto;" onclick="testEmail()">Test Email Dispatch</button>
                        </div>
                    </form>
                </div>

                <!-- System Health & Info -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <div style="background: var(--glass-bg); padding: 24px; border-radius: 24px; border: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: 16px;">System Info</h4>
                        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">PHP Version:</span>
                                <span><?php echo phpversion(); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">Database:</span>
                                <span style="color: var(--success);">Online</span>
                            </div>
                            <div class="form-group">
                                <span style="color: var(--text-muted);">Env Branding:</span>
                                <span>White-label Enabled</span>
                            </div>
                        </div>
                    </div>

                    <div style="background: linear-gradient(135deg, var(--primary), #4f46e5); padding: 24px; border-radius: 24px; color: white;">
                        <h4 style="margin-bottom: 8px;">Backup Reminder</h4>
                        <p style="font-size: 12px; opacity: 0.9; line-height: 1.6;">Always export your database before making major configuration changes.</p>
                        <button onclick="alert('Backup engine initiated...')" style="margin-top: 16px; background: rgba(255,255,255,0.2); border: none; padding: 10px 15px; border-radius: 12px; color: white; cursor: pointer; font-size: 12px; font-weight: 600;">Download SQL Backup</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveSetBtn');
            const originalText = btn.innerText;
            btn.innerText = 'Updating...';
            btn.disabled = true;

            const formData = new FormData(this);
            fetch('../../actions/update_settings.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.innerText = originalText;
                btn.disabled = false;
                if(data.success) {
                    alert('Settings updated successfully! The system will now refresh to apply changes.');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
        function testEmail() {
            const email = prompt("Enter an email address to send a test message to:", "<?php echo $settings['company_email'] ?? ''; ?>");
            if (!email) return;
            
            const btn = event.target;
            const originalText = btn.innerText;
            btn.innerText = "Testing...";
            btn.disabled = true;

            fetch('../../actions/send_invoice_email.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ 
                    id: 0, 
                    email: email, 
                    is_test: true 
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.innerText = originalText;
                btn.disabled = false;
                if(data.success) {
                    alert("Test Success! Please check your inbox at " + email);
                } else {
                    alert("Test Failed: " + data.message);
                }
            })
            .catch(err => {
                btn.innerText = originalText;
                btn.disabled = false;
                alert("Error connecting to server.");
            });
        }
    </script>
</body>
</html>
