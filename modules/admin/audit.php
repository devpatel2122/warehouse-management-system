<?php
/**
 * Warehouse Pro - Audit Logs
 * Role: Security & Oversight
 */
$page_title = 'Audit Logs';
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
                <h1 style="font-size: 24px; font-weight: 700;">Security Audit Logs</h1>
                <p style="color: var(--text-muted);">Track all critical system activities and user operations.</p>
            </header>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Target ID</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT al.*, u.username 
                                           FROM audit_logs al 
                                           JOIN users u ON al.user_id = u.id 
                                           ORDER BY al.created_at DESC LIMIT 100");
                        while($log = $res->fetch_assoc()):
                        ?>
                        <tr>
                            <td style="font-size: 13px; color: var(--text-muted);"><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></td>
                            <td><span class="badge" style="background: var(--glass-bg); color: var(--text-main);"><?php echo $log['username']; ?></span></td>
                            <td style="font-weight: 600;"><?php echo $log['action']; ?></td>
                            <td><?php echo strtoupper($log['target_table'] ?: 'SYSTEM'); ?></td>
                            <td>#<?php echo $log['target_id'] ?: 'N/A'; ?></td>
                            <td style="font-size: 13px; color: var(--text-muted);"><?php echo $log['details']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
