<?php
$page_title = 'Departments';
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
    <title>Manage Departments | Warehouse System</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
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
            <header class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700;">Internal Departments</h1>
                    <p style="color: var(--text-muted);">Manage your warehouse sub-departments and authorized roles.</p>
                </div>
                <button class="btn" style="width: auto;" onclick="document.getElementById('deptModal').style.display='flex'">
                    <i class="fas fa-plus"></i> Add Department
                </button>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>Key Role</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Admin Office</td><td><code>admin</code></td><td><span class="badge badge-success">Active</span></td><td style="text-align: right;"><i class="fas fa-lock text-muted"></i></td></tr>
                        <tr><td>Product Management</td><td><code>product_dept</code></td><td><span class="badge badge-success">Active</span></td><td style="text-align: right;"><i class="fas fa-lock text-muted"></i></td></tr>
                        <tr><td>Purchase & Procurement</td><td><code>purchase_dept</code></td><td><span class="badge badge-success">Active</span></td><td style="text-align: right;"><i class="fas fa-lock text-muted"></i></td></tr>
                        <tr><td>Sales & Dispatch</td><td><code>sell_dept</code></td><td><span class="badge badge-success">Active</span></td><td style="text-align: right;"><i class="fas fa-lock text-muted"></i></td></tr>
                        <tr><td>Stock & Inventory</td><td><code>inventory_dept</code></td><td><span class="badge badge-success">Active</span></td><td style="text-align: right;"><i class="fas fa-lock text-muted"></i></td></tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
                <h3><i class="fas fa-info-circle"></i> System Note</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 10px;">These departments are pre-integrated with the application's role-based access control (RBAC) system. New administrative sub-departments can be added for reporting purposes, but core module access is governed by these primary roles.</p>
            </div>
        </main>
    </div>

    <!-- Modal for illustration -->
    <div id="deptModal" class="modal">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header" style="text-align: left;">
                <h2>Add Department</h2>
                <p>Register a new operational unit.</p>
            </div>
            <form onsubmit="alert('New department metadata registered!'); document.getElementById('deptModal').style.display='none'; return false;">
                <div class="form-group">
                    <label>Department Name</label>
                    <input type="text" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Department Head</label>
                    <input type="text" class="form-input">
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('deptModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn">Register</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
