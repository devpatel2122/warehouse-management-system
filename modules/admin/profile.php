<?php
$page_title = 'My Profile';
require_once '../../includes/db.php';
$base_path = '../../';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $res->fetch_assoc();
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
                <h1 style="font-size: 24px; font-weight: 700;">Account Settings</h1>
                <p style="color: var(--text-muted);">Manage your personal information and security.</p>
            </header>

            <div style="display: grid; grid-template-columns: 350px 1fr; gap: 30px;">
                <!-- Avatar & Status -->
                <div style="background: var(--card-bg); padding: 30px; border-radius: 24px; border: 1px solid var(--border-color); text-align: center;">
                    <div style="position: relative; width: 150px; height: 150px; margin: 0 auto 20px;">
                        <?php 
                        $avatar = $user['avatar_path'] ? $base_path . $user['avatar_path'] : 'https://ui-avatars.com/api/?name='.urlencode($user['username']).'&background=6366f1&color=fff&size=128';
                        ?>
                        <img src="<?php echo $avatar; ?>" id="avatarPreview" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary);">
                        <label for="avatarInput" style="position: absolute; bottom: 0; right: 0; background: var(--primary); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: white; border: 3px solid var(--card-bg);">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                    <h2 style="font-size: 20px; margin-bottom: 5px;"><?php echo htmlspecialchars($user['username']); ?></h2>
                    <span class="badge badge-success" style="padding: 6px 16px;"><?php echo strtoupper($user['role']); ?></span>
                    
                    <div style="margin-top: 30px; text-align: left; font-size: 13px; color: var(--text-muted);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Member Since:</span>
                            <span style="color: var(--text-main);"><?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Security Level:</span>
                            <span style="color: var(--success);">Encryption v2.0</span>
                        </div>
                    </div>
                </div>

                <!-- Forms -->
                <div style="display: flex; flex-direction: column; gap: 30px;">
                    <div style="background: var(--card-bg); padding: 30px; border-radius: 24px; border: 1px solid var(--border-color);">
                        <h3 style="margin-bottom: 24px;"><i class="fas fa-user-edit"></i> Account Details</h3>
                        <form id="profileForm" enctype="multipart/form-data">
                            <input type="file" name="avatar" id="avatarInput" style="display: none;" onchange="document.getElementById('avatarPreview').src = window.URL.createObjectURL(this.files[0])">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-input" value="<?php echo $user['username']; ?>" readonly style="opacity: 0.6; cursor: not-allowed;">
                                </div>
                                <div class="form-group">
                                    <label>Display Email</label>
                                    <input type="email" name="email" class="form-input" value="<?php echo $user['email']; ?>" required>
                                </div>
                            </div>
                            
                            <div style="background: var(--glass-bg); padding: 20px; border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 20px;">
                                <div style="display: flex; align-items: flex-start; gap: 15px;">
                                    <div style="background: var(--primary); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                                        <i class="fas fa-shield-halved"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin-bottom: 5px; font-size: 16px;">Two-Factor Authentication</h4>
                                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">Add an extra layer of security to your account by requiring an email code at login.</p>
                                        
                                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; user-select: none;">
                                            <input type="checkbox" name="2fa_enabled" <?php echo ($user['2fa_enabled'] == 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; accent-color: var(--primary);">
                                            <span style="color: var(--text-main); font-weight: 500;">Enable 2FA via Email</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn" style="width: auto; padding: 12px 30px;">Save Profile Changes</button>
                        </form>
                    </div>

                <div style="background: var(--card-bg); padding: 30px; border-radius: 24px; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-shield-alt"></i> Security</h3>
                    <form id="passwordForm">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-input" required>
                        </div>
                        <button type="submit" class="btn" style="background: var(--secondary);">Change Password</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_info');

            fetch('../../actions/update_profile.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => alert(data.message));
        });

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_password');

            if(formData.get('new_password') !== formData.get('confirm_password')) {
                alert('New passwords do not match!');
                return;
            }

            fetch('../../actions/update_profile.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if(data.success) this.reset();
                });
        });
    </script>
</body>
</html>
