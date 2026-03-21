<?php
$page_title = 'Categories';
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
    <title>Manage Categories | Warehouse System</title>
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
                    <h1 style="font-size: 24px; font-weight: 700;">Product Categories</h1>
                    <p style="color: var(--text-muted);">Organize your products with categories and descriptions.</p>
                </div>
                <button class="btn" style="width: auto;" onclick="document.getElementById('categoryModal').style.display='flex'">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </header>

            <div class="table-container">
                <table id="categoryTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        <!-- Ajax data -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="categoryModal" class="modal">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header" style="text-align: left;">
                <h2>New Category</h2>
                <p>Add a new category to group your products.</p>
            </div>
            <form id="addCategoryForm">
                <div class="form-group">
                    <label>Category ID (Optional)</label>
                    <input type="number" name="id" class="form-input" placeholder="Leave blank for auto-increment">
                </div>
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-input" style="height: 100px;"></textarea>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('categoryModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn">Save Category</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header" style="text-align: left;">
                <h2>Edit Category</h2>
                <p>Update existing category information.</p>
            </div>
            <form id="editCategoryForm">
                <input type="hidden" name="old_cid" id="edit_category_id">
                <div class="form-group">
                    <label>Category ID (Primary Key)</label>
                    <input type="number" name="new_cid" id="edit_new_id" class="form-input" required title="Caution: Changing the ID will update all linked products.">
                </div>
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" id="edit_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" class="form-input" style="height: 100px;"></textarea>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('editCategoryModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn">Update Category</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function loadCategories() {
            fetch('../../actions/get_categories.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('categoryTableBody');
                    tbody.innerHTML = '';
                    data.forEach(cat => {
                        tbody.innerHTML += `
                            <tr>
                                <td>#${cat.id}</td>
                                <td style="font-weight: 600;">${cat.name}</td>
                                <td style="color: var(--text-muted);">${cat.description || 'No description'}</td>
                                <td style="text-align: right;">
                                    <button class="badge badge-success" onclick='openEditModal(${cat.id}, ${JSON.stringify(cat.name).replace(/'/g, "&apos;")}, ${JSON.stringify(cat.description || "").replace(/'/g, "&apos;")})' style="border:none; cursor:pointer; margin-right:5px;">Edit</button>
                                    <button class="badge badge-danger" onclick="deleteCategory(${cat.id})" style="border:none; cursor:pointer;">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../actions/add_category.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    document.getElementById('categoryModal').style.display='none';
                    this.reset();
                    loadCategories();
                } else alert(data.message);
            });
        });

        function openEditModal(id, name, description) {
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_new_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('editCategoryModal').style.display = 'flex';
        }

        document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../actions/update_category.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    document.getElementById('editCategoryModal').style.display='none';
                    loadCategories();
                } else alert(data.message);
            });
        });

        function deleteCategory(id) {
            if(!confirm('Are you sure you want to delete this category? Products in this category will be moved to uncategorized.')) return;
            fetch('../../actions/delete_category.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if(data.success) loadCategories();
                    else alert(data.message);
                });
        }

        loadCategories();
    </script>
</body>
</html>
