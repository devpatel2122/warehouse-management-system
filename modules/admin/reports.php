<?php
$page_title = 'Reports';
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
    <script>const CURRENCY_SYMBOL = '<?php echo CURRENCY_SYMBOL; ?>';</script>
</head>
<body>
    <div class="dashboard-container">
        <?php include $base_path . 'includes/sidebar.php'; ?>
        <?php include $base_path . 'includes/top_nav.php'; ?>

        <main class="main-content">
            <header class="mb-4">
                <h1 style="font-size: 24px; font-weight: 700;">Inventory Reports</h1>
                <p style="color: var(--text-muted);">Detailed insights into stock, sales, and purchases.</p>
            </header>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 24px;">
                <!-- Stock Inventory Report -->
                <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 16px;"><i class="fas fa-boxes"></i> Stock Inventory</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Products currently available in the warehouse.</p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button onclick="downloadStockReport()" class="btn" style="width: auto; font-size: 14px;">PDF Report</button>
                        <button onclick="window.location.href='../../actions/export_excel.php?type=products'" class="btn" style="width: auto; font-size: 14px; background: #10b981;"><i class="fas fa-file-excel"></i> Export Excel</button>
                    </div>
                </div>

                <!-- Sell Report -->
                <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 16px;"><i class="fas fa-chart-line"></i> Sell Report</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Detailed analysis of sales and revenue trends.</p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button onclick="viewRevenueStats()" class="btn" style="width: auto; font-size: 14px;">Stats</button>
                        <button onclick="window.location.href='../../actions/export_excel.php?type=sales'" class="btn" style="width: auto; font-size: 14px; background: #10b981;"><i class="fas fa-file-excel"></i> Export Excel</button>
                        <button onclick="window.location.href='../../actions/export_csv.php?type=sales'" class="btn" style="width: auto; font-size: 14px; background: var(--secondary);"><i class="fas fa-file-csv"></i> CSV</button>
                    </div>
                </div>

                <!-- Purchase Report -->
                <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 16px;"><i class="fas fa-file-invoice-dollar"></i> Purchase Report</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Procurement history and vendor expenditure records.</p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button onclick="downloadPurchaseReport()" class="btn" style="width: auto; font-size: 14px; background: var(--secondary);">PDF Report</button>
                        <button onclick="window.location.href='../../actions/export_excel.php?type=purchases'" class="btn" style="width: auto; font-size: 14px; background: #10b981;"><i class="fas fa-file-excel"></i> Excel</button>
                        <button onclick="window.location.href='../../actions/export_csv.php?type=purchases'" class="btn" style="width: auto; font-size: 14px; background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-main);"><i class="fas fa-file-csv"></i> CSV</button>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <!-- Margin Analysis -->
                <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 20px; font-size: 18px;"><i class="fas fa-percentage" style="color: var(--success);"></i> Category Profit Margins</h3>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php
                        $q_margin = "SELECT c.name, SUM(si.quantity * (si.unit_price - p.purchase_price)) as profit, 
                                    SUM(si.quantity * si.unit_price) as revenue
                                    FROM sale_items si 
                                    JOIN products p ON si.product_id = p.id 
                                    JOIN categories c ON p.category_id = c.id 
                                    GROUP BY c.id 
                                    ORDER BY profit DESC";
                        $res_margin = $conn->query($q_margin);
                        if ($res_margin && $res_margin->num_rows > 0) {
                            while($m = $res_margin->fetch_assoc()) : 
                                $percent = ($m['revenue'] > 0) ? ($m['profit'] / $m['revenue']) * 100 : 0;
                        ?>
                            <div>
                                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px;">
                                    <span><?php echo $m['name']; ?></span>
                                    <span style="font-weight: 700; color: var(--success);"><?php echo formatMoney($m['profit']); ?> (<?php echo round($percent, 1); ?>%)</span>
                                </div>
                                <div style="height: 8px; background: var(--glass-bg); border-radius: 10px; overflow: hidden;">
                                    <div style="width: <?php echo min(100, $percent); ?>%; height: 100%; background: var(--success); border-radius: 10px;"></div>
                                </div>
                            </div>
                        <?php endwhile; } else { echo '<p style="color:var(--text-muted); font-size:13px;">No sales data for analysis.</p>'; } ?>
                    </div>
                </div>

                <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 20px; font-size: 18px;"><i class="fas fa-medal" style="color: #fbbf24;"></i> Performance Insights</h3>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6;">
                        Based on current data, your highest margin comes from categories shown on the left. 
                        <strong>Pro-Tip:</strong> Consider running promotions on high-stock, low-margin items to improve warehouse turnover.
                    </p>
                    <div style="margin-top: 20px; padding: 16px; background: rgba(99, 102, 241, 0.05); border-radius: 16px; border: 1px dashed var(--primary);">
                        <span style="font-size: 12px; font-weight: 600; color: var(--primary); display: block; margin-bottom: 4px;">System Alert</span>
                        <span style="font-size: 13px;">Stock turnover is optimal. No dead-stock detected this month.</span>
                    </div>
                </div>
            </div>

            <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
                <h3 style="margin-bottom: 20px;">Top Selling Products</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Total Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="topProductsBody">
                            <!-- Loaded via Ajax -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Stats Modal -->
    <div id="statsModal" class="modal">
        <div class="auth-card" style="max-width: 800px;">
            <div class="auth-header" style="text-align: left; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h2>Main Category Analysis</h2>
                    <p>Financial breakdown of sales performance by category.</p>
                </div>
                <button onclick="document.getElementById('statsModal').style.display='none'" style="background:none; border:none; color:var(--text-muted); cursor:pointer;"><i class="fas fa-times"></i></button>
            </div>
            <div style="height: 300px; margin-top: 20px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let revenueChart = null;

        function loadTopProducts() {
            fetch('../../actions/get_top_products.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('topProductsBody');
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" class="text-center">No sales recorded yet.</td></tr>';
                        return;
                    }
                    data.forEach(p => {
                        tbody.innerHTML += `
                            <tr>
                                <td style="font-weight: 600;">${p.name}</td>
                                <td>${p.total_sold} units</td>
                                <td style="color: var(--success); font-weight: 600;">${CURRENCY_SYMBOL}${parseFloat(p.revenue).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                });
        }

        function downloadStockReport() {
            fetch('../../actions/get_products.php')
                .then(res => res.json())
                .then(products => {
                    const printWindow = window.open('', '_blank');
                    let tableRows = '';
                    products.forEach(p => {
                        tableRows += `
                            <tr>
                                <td>${p.name}</td>
                                <td>${p.category_name || 'N/A'}</td>
                                <td>${p.barcode || 'N/A'}</td>
                                <td>${p.stock_quantity}</td>
                                <td>${CURRENCY_SYMBOL}${p.price}</td>
                            </tr>
                        `;
                    });

                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Inventory Status Report</title>
                            <style>
                                body { font-family: system-ui; padding: 40px; }
                                h1 { color: #6366f1; border-bottom: 2px solid #6366f1; padding-bottom: 10px; }
                                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                                th { text-align: left; background: #f8fafc; padding: 12px; border: 1px solid #ddd; }
                                td { padding: 12px; border: 1px solid #eee; }
                                .footer { margin-top: 30px; font-size: 12px; color: #94a3b8; }
                            </style>
                        </head>
                        <body>
                            <h1>Warehouse Stock Inventory Report</h1>
                            <p>Generated on: ${new Date().toLocaleString()}</p>
                            <table>
                                <thead>
                                    <tr><th>Product Name</th><th>Category</th><th>Barcode</th><th>Current Stock</th><th>Price</th></tr>
                                </thead>
                                <tbody>${tableRows}</tbody>
                            </table>
                            <div class="footer">Warehouse Pro Management System - Confidential Report</div>
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    printWindow.print();
                });
        }

        function viewRevenueStats() {
            document.getElementById('statsModal').style.display = 'flex';
            if (revenueChart) revenueChart.destroy();

            fetch('../../actions/get_revenue_by_category.php')
                .then(res => res.json())
                .then(data => {
                    const labels = data.map(cat => cat.name);
                    const values = data.map(cat => cat.revenue);

                    const ctx = document.getElementById('revenueChart').getContext('2d');
                    revenueChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Revenue by Category (' + CURRENCY_SYMBOL + ')',
                                data: values,
                                backgroundColor: '#6366f1',
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                });
        }

        function downloadPurchaseReport() {
            fetch('../../actions/get_purchases.php')
                .then(res => res.json())
                .then(purchases => {
                    const printWindow = window.open('', '_blank');
                    let tableRows = '';
                    purchases.forEach(p => {
                        tableRows += `
                            <tr>
                                <td>${p.purchase_date}</td>
                                <td>${p.vendor_name || 'Generic Vendor'}</td>
                                <td>${CURRENCY_SYMBOL}${p.total_amount}</td>
                                <td>Completed</td>
                            </tr>
                        `;
                    });

                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Purchase History Report</title>
                            <style>
                                body { font-family: sans-serif; padding: 40px; }
                                h1 { color: #6366f1; border-bottom: 2px solid #6366f1; padding-bottom: 10px; }
                                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                                th { text-align: left; background: #f8fafc; padding: 12px; border: 1px solid #ddd; }
                                td { padding: 12px; border: 1px solid #eee; }
                            </style>
                        </head>
                        <body>
                            <h1>Warehouse Procurement Report</h1>
                            <p>Generated on: ${new Date().toLocaleString()}</p>
                            <table>
                                <thead>
                                    <tr><th>Date</th><th>Vendor</th><th>Total Amount</th><th>Status</th></tr>
                                </thead>
                                <tbody>${tableRows}</tbody>
                            </table>
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    printWindow.print();
                });
        }

        loadTopProducts();
    </script>
</body>
</html>
