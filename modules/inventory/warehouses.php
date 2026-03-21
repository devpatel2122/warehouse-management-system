<?php
/**
 * Warehouse Pro - Warehouse Management
 * Role: Admin / Inventory
 */
$page_title = 'Warehouses';
require_once '../../includes/db.php';
$base_path = '../../';

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
</head>
<body>
    <div class="dashboard-container">
        <?php include $base_path . 'includes/sidebar.php'; ?>
        <?php include $base_path . 'includes/top_nav.php'; ?>

        <main class="main-content">
            <header class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700;">Multi-Location Management</h1>
                    <p style="color: var(--text-muted);">Track stock across different physical warehouses.</p>
                </div>
                <button class="btn" onclick="openWarehouseModal()" style="width: auto;"><i class="fas fa-plus"></i> Add New Warehouse</button>
            </header>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Warehouse Name</th>
                            <th>Address / Location</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="warehouseTableBody">
                        <?php
                        $res = $conn->query("SELECT * FROM warehouses ORDER BY id DESC");
                        while($w = $res->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?php echo $w['id']; ?></td>
                            <td style="font-weight: 600;"><?php echo $w['name']; ?></td>
                            <td><?php echo $w['location']; ?></td>
                            <td><i class="fas fa-user-tie" style="opacity: 0.5;"></i> <?php echo $w['contact_person'] ?: 'N/A'; ?></td>
                            <td><?php echo $w['phone'] ?: 'N/A'; ?></td>
                            <td>
                                <button class="btn btn-sm" onclick='editWarehouse(<?php echo json_encode($w); ?>)' style="background: var(--glass-bg); color: var(--text-main);"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Warehouse Modal -->
    <div id="warehouseModal" class="modal" style="display: none; align-items: center; justify-content: center;">
        <div style="background: var(--card-bg); padding: 32px; border-radius: 24px; width: 100%; max-width: 500px; border: 1px solid var(--border-color);">
            <h3 id="modalTitle" style="margin-bottom: 24px;">Add Warehouse</h3>
            <form id="warehouseForm">
                <input type="hidden" name="id" id="warehouseId">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Warehouse Name</label>
                    <input type="text" name="name" id="warehouseName" class="form-input" required placeholder="e.g. Mumbai Logistics Park">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Location / Address</label>
                    <input type="text" name="location" id="warehouseLocation" class="form-input" required placeholder="Full physical address">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" name="contact_person" id="warehouseContact" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" id="warehousePhone" class="form-input">
                    </div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn">Save Warehouse</button>
                    <button type="button" class="btn" style="background: var(--glass-bg); color: var(--text-main);" onclick="closeWarehouseModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openWarehouseModal() {
            document.getElementById('modalTitle').innerText = 'Add New Warehouse';
            document.getElementById('warehouseForm').reset();
            document.getElementById('warehouseId').value = '';
            document.getElementById('warehouseModal').style.display = 'flex';
        }

        function closeWarehouseModal() {
            document.getElementById('warehouseModal').style.display = 'none';
        }

        function editWarehouse(w) {
            document.getElementById('modalTitle').innerText = 'Edit Warehouse';
            document.getElementById('warehouseId').value = w.id;
            document.getElementById('warehouseName').value = w.name;
            document.getElementById('warehouseLocation').value = w.location;
            document.getElementById('warehouseContact').value = w.contact_person;
            document.getElementById('warehousePhone').value = w.phone;
            document.getElementById('warehouseModal').style.display = 'flex';
        }

        document.getElementById('warehouseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../actions/save_warehouse.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
    </script>
</body>
</html>
