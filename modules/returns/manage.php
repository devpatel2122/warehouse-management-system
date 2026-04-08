<?php
$page_title = 'Returns Management';
require_once '../../includes/db.php';
$base_path = '../../';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'index.php');
    exit();
}

// Admin and Sell Dept (or a dedicated Return Dept) can access
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'sell_dept') {
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
            <header class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700;">Sales Returns</h1>
                    <p style="color: var(--text-muted);">Process and track returned goods and refunds.</p>
                </div>
                <button class="btn" style="width: auto;" onclick="document.getElementById('returnModal').style.display='flex'">
                    <i class="fas fa-undo"></i> Initiate Return
                </button>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice #</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="returnsTableBody">
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 40px; color: var(--text-muted);">
                                <i class="fas fa-box-open" style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                No returns processed yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Return Modal -->
    <div id="returnModal" class="modal">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header" style="text-align: left;">
                <h2>Process Return</h2>
                <p>Verify product condition before accepting restock.</p>
            </div>
            <form id="returnForm">
                <div class="form-group">
                    <label>Invoice Number</label>
                    <input type="text" id="invoiceNumber" class="form-input" placeholder="INV-XXXXX" required onblur="fetchInvoiceItems()">
                    <div id="invoiceLoading" style="display:none; font-size:12px; color:var(--primary); margin-top:5px;"><i class="fas fa-spinner fa-spin"></i> Fetching invoice details...</div>
                </div>

                <div id="itemsContainer" style="display:none; margin-top: 15px;">
                    <label style="font-size: 13px; color: var(--text-muted); text-transform: uppercase;">Sold Items (Select to Return)</label>
                    <div id="itemsList" style="margin-top: 10px; max-height: 250px; overflow-y: auto; border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; background: rgba(0,0,0,0.1);">
                        <!-- Dynamic items -->
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Reason for Return</label>
                    <select id="returnReason" class="form-input">
                        <option>Damaged on Arrival</option>
                        <option>Wrong Specification</option>
                        <option>Customer Dissatisfaction</option>
                        <option>Expired Stock</option>
                        <option>Other</option>
                    </select>
                </div>
                <!-- Action selection... rest of form -->
                <div class="form-group">
                    <label>Action</label>
                    <div style="display: flex; gap: 20px; color: var(--text-muted); font-size: 14px;">
                        <label><input type="radio" name="returnAction" value="restock" checked> Restock Item</label>
                        <label><input type="radio" name="returnAction" value="discard"> Discard/Write-off</label>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('returnModal').style.display='none'">Cancel</button>
                    <button type="submit" id="submitReturnBtn" class="btn" disabled>Confirm Return</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentSale = null;
        let currentItems = [];

        function loadReturns() {
            fetch('../../actions/get_returns.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('returnsTableBody');
                    if (data.success && data.returns.length > 0) {
                        tbody.innerHTML = '';
                        data.returns.forEach(ret => {
                            const date = new Date(ret.return_date).toLocaleDateString();
                            tbody.innerHTML += `
                                <tr>
                                    <td>${ret.id}</td>
                                    <td style="font-weight:600;">${ret.invoice_no}</td>
                                    <td>${ret.product_name}</td>
                                    <td>${ret.quantity}</td>
                                    <td>${ret.reason}</td>
                                    <td>${date}</td>
                                    <td style="text-align: right;">
                                        <span class="badge ${ret.action === 'restock' ? 'badge-success' : 'badge-danger'}">${ret.action}</span>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 40px; color: var(--text-muted);">
                                    <i class="fas fa-box-open" style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                    No returns processed yet.
                                </td>
                            </tr>
                        `;
                    }
                });
        }

        function fetchInvoiceItems() {
            const invoiceNo = document.getElementById('invoiceNumber').value.trim();
            if (!invoiceNo) return;

            document.getElementById('invoiceLoading').style.display = 'block';
            document.getElementById('itemsContainer').style.display = 'none';
            document.getElementById('submitReturnBtn').disabled = true;

            fetch('../../actions/get_sale_by_invoice.php?invoice_no=' + encodeURIComponent(invoiceNo))
                .then(res => res.json())
                .then(data => {
                    document.getElementById('invoiceLoading').style.display = 'none';
                    if (data.success) {
                        currentSale = data.sale;
                        currentItems = data.items;
                        renderItems(data.items);
                        document.getElementById('itemsContainer').style.display = 'block';
                    } else {
                        alert(data.message || 'Invoice not found.');
                        currentSale = null;
                        currentItems = [];
                    }
                })
                .catch(err => {
                    document.getElementById('invoiceLoading').style.display = 'none';
                    alert('Error: ' + err.message);
                });
        }

        function renderItems(items) {
            const list = document.getElementById('itemsList');
            list.innerHTML = '';
            
            if (items.length === 0) {
                list.innerHTML = '<div style="text-align:center; padding:10px; color:var(--text-muted);">No items found in this invoice.</div>';
                return;
            }

            items.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid var(--glass-border);';
                
                itemDiv.innerHTML = `
                    <div style="display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" class="return-item-check" data-id="${item.id}" data-pid="${item.product_id}" data-max-qty="${item.quantity}" onchange="toggleSubmitBtn()">
                        <div>
                            <div style="font-weight:600; font-size:14px;">${item.product_name}</div>
                            <div style="font-size:11px; color:var(--text-muted);">Sold: ${item.quantity} × ₹${item.unit_price}</div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <label style="font-size:11px;">Return Qty:</label>
                        <input type="number" class="return-item-qty form-input" style="width:60px; height:25px; padding:2px 5px; font-size:12px;" value="${item.quantity}" min="0.1" step="0.1" max="${item.quantity}">
                    </div>
                `;
                list.appendChild(itemDiv);
            });
        }

        function toggleSubmitBtn() {
            const checks = document.querySelectorAll('.return-item-check:checked');
            document.getElementById('submitReturnBtn').disabled = checks.length === 0;
        }

        document.getElementById('returnForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedItems = [];
            const checks = document.querySelectorAll('.return-item-check:checked');
            
            checks.forEach(check => {
                const container = check.parentElement.parentElement;
                const qtyInput = container.querySelector('.return-item-qty');
                
                selectedItems.push({
                    sale_item_id: check.getAttribute('data-id'),
                    product_id: check.getAttribute('data-pid'),
                    qty: qtyInput.value,
                    reason: document.getElementById('returnReason').value,
                    action: document.querySelector('input[name="returnAction"]:checked').value
                });
            });

            if (selectedItems.length === 0) {
                alert('Please select at least one item to return.');
                return;
            }

            const submitBtn = document.getElementById('submitReturnBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            fetch('../../actions/process_return.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    invoice_no: document.getElementById('invoiceNumber').value,
                    items: selectedItems
                })
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Confirm Return';
                
                if (data.success) {
                    alert('Return processed successfully!');
                    document.getElementById('returnModal').style.display='none';
                    this.reset();
                    document.getElementById('itemsContainer').style.display = 'none';
                    loadReturns();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Confirm Return';
                alert('Network error: ' + err.message);
            });
        });

        // Initial load
        document.addEventListener('DOMContentLoaded', loadReturns);
    </script>
</body>
</html>
