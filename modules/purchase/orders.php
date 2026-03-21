<?php
$page_title = 'Purchase Orders';
require_once '../../includes/db.php';
$base_path = '../../';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'index.php');
    exit();
}
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'purchase_dept') {
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
                    <h1 style="font-size: 24px; font-weight: 700;">Purchase Stocks</h1>
                    <p style="color: var(--text-muted);">Buy products from vendors and replenish inventory.</p>
                </div>
                <button class="btn" style="width: auto;" onclick="openModal('purchaseModal')">
                    <i class="fas fa-plus"></i> New Purchase
                </button>
            </header>

            <div style="background: var(--card-bg); border-radius: 20px; border: 1px solid var(--border-color); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--glass-bg);">
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Date</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Vendor</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Total Amount</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600; text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseTableBody">
                        <!-- Content loaded via Ajax -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Purchase Modal -->
    <div id="purchaseModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
        <div class="auth-card" style="max-width: 600px;">
            <div class="auth-header" style="text-align: left;">
                <h2 style="font-size: 20px;">Record New Purchase</h2>
                <p>Add stock to inventory by recording a purchase from a vendor.</p>
            </div>
            <form id="purchaseForm">
                <div class="form-group">
                    <label>Vendor</label>
                    <select name="vendor_id" id="vendorSelect" class="form-input" required>
                        <option value="">Select Vendor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Product</label>
                    <select name="product_id" id="productSelect" class="form-input" required>
                        <option value="">Select Product</option>
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-input" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Unit Price - <?php echo CURRENCY_SYMBOL; ?></label>
                        <input type="number" step="0.01" name="unit_price" class="form-input" required>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="closeModal('purchaseModal')">Cancel</button>
                    <button type="submit" class="btn">Record Purchase</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
            if(id === 'purchaseModal') {
                loadVendors();
                loadProductsList();
            }
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function loadVendors() {
            fetch('../../actions/get_vendors.php').then(res => res.json()).then(data => {
                const select = document.getElementById('vendorSelect');
                select.innerHTML = '<option value="">Select Vendor</option>';
                data.forEach(v => select.innerHTML += `<option value="${v.id}">${v.name}</option>`);
            });
        }

        function loadProductsList() {
            fetch('../../actions/get_products.php').then(res => res.json()).then(data => {
                const select = document.getElementById('productSelect');
                select.innerHTML = '<option value="">Select Product</option>';
                data.forEach(p => select.innerHTML += `<option value="${p.id}">${p.name}</option>`);
            });
        }

        function loadPurchases() {
            fetch('../../actions/get_purchases.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('purchaseTableBody');
                    tbody.innerHTML = '';
                    data.forEach(p => {
                        tbody.innerHTML += `
                            <tr style="border-top: 1px solid var(--border-color);">
                                <td style="padding: 16px 24px;">${p.purchase_date}</td>
                                <td style="padding: 16px 24px;">${p.vendor_name || 'Generic'}</td>
                                <td style="padding: 16px 24px;">${CURRENCY_SYMBOL}${p.total_amount}</td>
                                <td style="padding: 16px 24px; text-align: right;"><span style="color: var(--success); background: rgba(16, 185, 129, 0.1); padding: 4px 12px; border-radius: 20px; font-size: 12px;">Completed</span></td>
                            </tr>
                        `;
                    });
                });
        }

        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../actions/add_purchase.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    closeModal('purchaseModal');
                    loadPurchases();
                    this.reset();
                } else {
                    alert(data.message);
                }
            });
        });

        loadPurchases();
    </script>
</body>
</html>
