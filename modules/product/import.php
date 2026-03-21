<?php
/**
 * Warehouse Pro - Bulk Product Import
 */
$page_title = 'Import Products';
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
                <h1 style="font-size: 24px; font-weight: 700;">Bulk Import</h1>
                <p style="color: var(--text-muted);">Quickly populate your inventory using CSV files.</p>
            </header>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                <div style="background: var(--card-bg); padding: 32px; border-radius: 24px; border: 1px solid var(--border-color);">
                    <form id="importForm" enctype="multipart/form-data">
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label>Select CSV File</label>
                            <input type="file" name="csv_file" class="form-input" accept=".csv" required style="padding: 10px;">
                        </div>
                        <div style="background: rgba(99, 102, 241, 0.05); padding: 20px; border-radius: 12px; border: 1px dashed var(--primary); margin-bottom: 24px;">
                            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;"><i class="fas fa-info-circle"></i> CSV Structure Required:</p>
                            <code style="font-size: 12px; color: var(--primary);">Name, CategoryID, SKU, Price, Stock</code>
                        </div>
                        <button type="submit" class="btn">Start Import Process</button>
                    </form>
                    <div id="importResult" style="margin-top: 20px;"></div>
                </div>

                <div style="background: var(--glass-bg); padding: 32px; border-radius: 24px; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 16px;">Download Sample</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">Use our pre-formatted template to ensure zero errors during the migration process.</p>
                    <a href="sample_products.csv" download class="btn" style="background: var(--secondary); width: auto;">Download Template .CSV</a>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('importForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const resultDiv = document.getElementById('importResult');
            resultDiv.innerHTML = '<p style="color: var(--primary);">Importing... Please wait.</p>';

            fetch('../../actions/import_products.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    resultDiv.innerHTML = `<div style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 15px; border-radius: 10px;">
                        <strong>Success!</strong> ${data.imported} products added successfully.
                    </div>`;
                } else {
                    resultDiv.innerHTML = `<div style="background: rgba(244, 63, 94, 0.1); color: var(--danger); padding: 15px; border-radius: 10px;">
                        <strong>Error:</strong> ${data.message}
                    </div>`;
                }
            });
        });
    </script>
</body>
</html>
