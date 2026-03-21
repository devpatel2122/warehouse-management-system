<?php
$page_title = 'Customers';
require_once '../../includes/db.php';
$base_path = '../../';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'index.php');
    exit();
}
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
    <title>Manage Customers | Warehouse System</title>
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
                    <h1 style="font-size: 24px; font-weight: 700;">Customer Database</h1>
                    <p style="color: var(--text-muted);">View records of frequent customers and billing info.</p>
                </div>
                <button class="btn" style="width: auto;" onclick="openNewCustomerModal()">
                    <i class="fas fa-user-plus"></i> New Customer
                </button>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>GSTIN</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                        <!-- Ajax data -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="customerModal" class="modal">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header" style="text-align: left;">
                <h2 id="modalTitle">Customer Registration</h2>
                <p>Enter details for the new customer record.</p>
            </div>
            <form id="addCustomerForm">
                <input type="hidden" name="id" id="customerId">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" id="cName" class="form-input" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" id="cPhone" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>GSTIN (Optional)</label>
                        <input type="text" name="gstin" id="cGstin" class="form-input" placeholder="27XXXXX0000X1Z5">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="cEmail" class="form-input">
                </div>
                <div class="form-group">
                    <label>Billing Address</label>
                    <textarea name="address" id="cAddress" class="form-input" style="height: 60px;"></textarea>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('customerModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn">Save Customer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function loadCustomers() {
            fetch('../../actions/get_customers.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('customerTableBody');
                    tbody.innerHTML = '';
                    data.forEach(c => {
                        tbody.innerHTML += `
                            <tr>
                                <td style="font-weight: 600;">${c.name}</td>
                                <td style="font-family: monospace; font-size: 13px;">${c.gstin || '<span style="opacity:0.3">N/A</span>'}</td>
                                <td>${c.phone}</td>
                                <td>${c.email || '-'}</td>
                                <td style="text-align: right;">
                                    <button class="badge badge-success" style="border:none; cursor:pointer;" onclick="editCustomer(${JSON.stringify(c).replace(/"/g, '&quot;')})">Edit</button>
                                    <button class="badge badge-danger" style="border:none; cursor:pointer; margin-left:5px;" onclick="deleteCustomer(${c.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        function openNewCustomerModal() {
            document.getElementById('customerModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = 'New Customer Registration';
            document.getElementById('addCustomerForm').reset();
            document.getElementById('customerId').value = '';
        }

        function editCustomer(c) {
            document.getElementById('customerModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = 'Edit Customer Details';
            document.getElementById('customerId').value = c.id;
            document.getElementById('cName').value = c.name;
            document.getElementById('cPhone').value = c.phone;
            document.getElementById('cGstin').value = c.gstin || '';
            document.getElementById('cEmail').value = c.email || '';
            document.getElementById('cAddress').value = c.address || '';
        }

        function deleteCustomer(id) {
            if(!confirm('Permanently delete this customer?')) return;
            fetch('../../actions/delete_customer.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if(data.success) loadCustomers();
                    else alert(data.message);
                });
        }

        document.getElementById('addCustomerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../actions/add_customer.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    document.getElementById('customerModal').style.display='none';
                    loadCustomers();
                } else alert(data.message);
            });
        });

        loadCustomers();
    </script>
</body>
</html>
