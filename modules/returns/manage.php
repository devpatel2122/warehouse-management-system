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
                    <input type="text" class="form-input" placeholder="INV-XXXXX" required>
                </div>
                <div class="form-group">
                    <label>Reason for Return</label>
                    <select class="form-input">
                        <option>Damaged on Arrival</option>
                        <option>Wrong Specification</option>
                        <option>Customer Dissatisfaction</option>
                        <option>Expired Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Action</label>
                    <div style="display: flex; gap: 20px; color: var(--text-muted); font-size: 14px;">
                        <label><input type="radio" name="action" checked> Restock Item</label>
                        <label><input type="radio" name="action"> Discard/Write-off</label>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('returnModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn">Confirm Return</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('returnForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Return request initiated and pending supervisor approval.');
            document.getElementById('returnModal').style.display='none';
            this.reset();
        });
    </script>
</body>
</html>
