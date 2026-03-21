<?php
$page_title = 'Sub-Categories';
require_once '../../includes/db.php';
$base_path = '../../';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'index.php');
    exit();
}
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'product_dept') {
    header('Location: ' . $base_path . 'dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sub-Categories | Warehouse System</title>
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
                    <h1 style="font-size: 24px; font-weight: 700;">Sub-Categories</h1>
                    <p style="color: var(--text-muted);">Define granular product categories for better organization.</p>
                </div>
                <button class="btn" style="width: auto;" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> New Sub-Category
                </button>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Main Category</th>
                            <th>Sub-Category</th>
                            <th>Description</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="subTableBody">
                        <!-- Ajax data -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="subModal" class="modal">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header" style="text-align: left;">
                <h2 id="modalTitle">Create Sub-Category</h2>
                <p>Link a sub-category to a parent category.</p>
            </div>
            <form id="addSubForm">
                <input type="hidden" name="id" id="subId">
                <div class="form-group">
                    <label>Parent Category</label>
                    <select name="category_id" id="parentSelect" class="form-input" required></select>
                </div>
                <div class="form-group">
                    <label>Sub-Category Name</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-input" style="height: 60px;"></textarea>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('subModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn">Link Category</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function loadMainCategories() {
            return fetch('../../actions/get_categories.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('parentSelect');
                    select.innerHTML = '';
                    data.forEach(cat => {
                        select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                    });
                });
        }

        function loadSubCategories() {
            fetch('../../actions/get_sub_categories.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('subTableBody');
                    tbody.innerHTML = '';
                    data.forEach(sub => {
                        tbody.innerHTML += `
                            <tr>
                                <td><span class="badge badge-warning">${sub.category_name}</span></td>
                                <td style="font-weight: 600;">${sub.name}</td>
                                <td style="color: var(--text-muted);">${sub.description || '-'}</td>
                                <td style="text-align: right;">
                                    <button class="badge badge-primary" onclick="editSub(${JSON.stringify(sub).replace(/"/g, '&quot;')})" style="border:none; cursor:pointer; margin-right: 5px;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="badge badge-danger" onclick="deleteSub(${sub.id})" style="border:none; cursor:pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        function deleteSub(id) {
            if(confirm('Are you sure you want to delete this sub-category?')) {
                fetch(`../../actions/delete_sub_category.php?id=${id}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) loadSubCategories();
                        else alert(data.message);
                    });
            }
        }

        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Create Sub-Category';
            document.getElementById('subId').value = '';
            document.getElementById('addSubForm').reset();
            loadMainCategories();
            document.getElementById('subModal').style.display = 'flex';
        }

        function editSub(sub) {
            document.getElementById('modalTitle').innerText = 'Edit Sub-Category';
            document.getElementById('subId').value = sub.id;
            document.getElementById('addSubForm').elements['name'].value = sub.name;
            document.getElementById('addSubForm').elements['description'].value = sub.description;
            
            loadMainCategories().then(() => {
                document.getElementById('parentSelect').value = sub.category_id;
                document.getElementById('subModal').style.display = 'flex';
            });
        }

        document.getElementById('addSubForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../actions/add_sub_category.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    document.getElementById('subModal').style.display='none';
                    document.getElementById('modalTitle').innerText = 'Create Sub-Category';
                    document.getElementById('subId').value = '';
                    this.reset();
                    loadSubCategories();
                } else alert(data.message);
            });
        });

        loadSubCategories();
    </script>
</body>
</html>
