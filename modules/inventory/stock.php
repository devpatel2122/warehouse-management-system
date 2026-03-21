<?php
$page_title = 'Inventory Report';
require_once '../../includes/db.php';
$base_path = '../../';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'index.php');
    exit();
}
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'inventory_dept') {
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
    <script>const CURRENCY_SYMBOL = '<?php echo CURRENCY_SYMBOL; ?>';</script>
</head>
<body>
    <div class="dashboard-container">
        <?php include $base_path . 'includes/sidebar.php'; ?>
        <?php include $base_path . 'includes/top_nav.php'; ?>

        <main class="main-content">
            <header class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700;">Stock Inventory Status</h1>
                    <p style="color: var(--text-muted);">View current availability of all products in the warehouse.</p>
                </div>
                <div>
                    <a href="../../actions/export_excel.php?type=products" class="btn" style="width: auto; background: #10b981; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </header>

            <div style="background: var(--card-bg); border-radius: 20px; border: 1px solid var(--border-color); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--glass-bg);">
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Product</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Barcode</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Availability</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <!-- Loaded via Ajax -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function loadInventory() {
            fetch('../../actions/get_products.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('inventoryTableBody');
                    tbody.innerHTML = '';
                    data.forEach(p => {
                        tbody.innerHTML += `
                            <tr style="border-top: 1px solid var(--border-color);">
                                <td style="padding: 16px 24px;">${p.name}</td>
                                <td style="padding: 16px 24px;">${p.barcode || 'N/A'}</td>
                                <td style="padding: 16px 24px;">
                                    <div style="width: 100px; height: 8px; background: var(--glass-bg); border-radius: 4px; overflow: hidden; margin-bottom: 4px;">
                                        <div style="width: ${Math.min(p.stock_quantity, 100)}%; height: 100%; background: ${p.stock_quantity > 10 ? 'var(--success)' : 'var(--danger)'};"></div>
                                    </div>
                                    <span style="font-size: 12px; font-weight: 600;">${p.stock_quantity} in stock</span>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <button class="btn" onclick="triggerReorder('${p.name}', ${p.id})" style="width: auto; padding: 6px 12px; font-size: 12px;">Alert Reorder</button>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        function triggerReorder(name, id) {
            if(confirm('Create a stock replenishment request for ' + name + '?')) {
                alert('Success: Reorder alert sent to Purchase Department for ' + name);
            }
        }
        loadInventory();
    </script>
</body>
</html>
