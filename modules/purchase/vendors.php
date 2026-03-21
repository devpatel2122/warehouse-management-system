<?php
$page_title = 'Vendors';
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
    <title>Manage Vendors | Warehouse System</title>
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
                    <h1 style="font-size: 24px; font-weight: 700;">Purchase Vendors</h1>
                    <p style="color: var(--text-muted);">Manage your supply chain and contact information.</p>
                </div>
                <button class="btn" style="width: auto;" onclick="document.getElementById('vendorModal').style.display='flex'">
                    <i class="fas fa-plus"></i> Add Vendor
                </button>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="vendorTableBody">
                        <!-- Ajax data -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="vendorModal" class="modal">
        <div class="auth-card" style="max-width: 600px;">
            <div class="auth-header" style="text-align: left;">
                <h2>New Vendor Details</h2>
                <p>Register a new supplier in the system.</p>
            </div>
            <form id="addVendorForm">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" name="contact_person" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label>Office Address</label>
                    <textarea name="address" class="form-input" style="height: 60px;"></textarea>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('vendorModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn">Register Vendor</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function loadVendors() {
            fetch('../../actions/get_vendors.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('vendorTableBody');
                    tbody.innerHTML = '';
                    data.forEach(v => {
                        tbody.innerHTML += `
                            <tr>
                                <td style="font-weight: 600;">${v.name}</td>
                                <td>${v.contact_person || '-'}</td>
                                <td>${v.phone || '-'}</td>
                                <td style="color: var(--primary);">${v.email || '-'}</td>
                                <td style="text-align: right;">
                                    <button class="badge badge-success" style="border:none; cursor:pointer;" onclick="editVendor(${JSON.stringify(v).replace(/"/g, '&quot;')})">Edit</button>
                                    <button class="badge badge-danger" style="border:none; cursor:pointer; margin-left:8px;" onclick="deleteVendor(${v.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        function editVendor(v) {
            document.getElementById('vendorModal').style.display = 'flex';
            document.querySelector('#vendorModal h2').innerText = 'Edit Vendor Details';
            const form = document.getElementById('addVendorForm');
            form.elements['name'].value = v.name;
            form.elements['contact_person'].value = v.contact_person;
            form.elements['phone'].value = v.phone;
            form.elements['email'].value = v.email;
            form.elements['address'].value = v.address;

            if(!form.elements['id']) {
                const hid = document.createElement('input');
                hid.type = 'hidden';
                hid.name = 'id';
                form.appendChild(hid);
            }
            form.elements['id'].value = v.id;
        }

        function deleteVendor(id) {
            if(!confirm('Delete this vendor? This may affect purchase records.')) return;
            fetch('../../actions/delete_vendor.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if(data.success) loadVendors();
                    else alert(data.message);
                });
        }

        document.getElementById('addVendorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../actions/add_vendor.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    document.getElementById('vendorModal').style.display='none';
                    this.reset();
                    loadVendors();
                } else alert(data.message);
            });
        });

        loadVendors();
    </script>
</body>
</html>
