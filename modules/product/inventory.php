<?php
/**
 * Warehouse Pro - Inventory Management
 * Module: Product/Inventory
 */
$page_title = 'Inventory';
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
    <title><?php echo $page_title; ?> | Warehouse System</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <script>window.CURRENCY_SYMBOL = '<?php echo CURRENCY_SYMBOL; ?>';</script>
</head>
<body>
    <div class="dashboard-container">
        <?php include $base_path . 'includes/sidebar.php'; ?>
        <?php include $base_path . 'includes/top_nav.php'; ?>

        <main class="main-content">
            <header class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700;">Inventory Management</h1>
                    <p style="color: var(--text-muted);">Manage products, barcodes, and profit margins.</p>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="../../actions/export_excel.php?type=products" class="btn" style="width: auto; background: #10b981; display: flex; align-items: center; gap: 8px; text-decoration: none;">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <button class="btn" style="width: auto; background: var(--secondary);" onclick="exportCSV()">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    <button class="btn" style="width: auto;" onclick="openModal('productModal')">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
            </header>

            <div style="background: var(--card-bg); border-radius: 20px; border: 1px solid var(--border-color); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--glass-bg);">
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Image</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Barcode/Serial</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Product Name</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Stock</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Location</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Buy Price</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600;">Sell Price</th>
                            <th style="padding: 16px 24px; color: var(--text-muted); font-weight: 600; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Product Form Modal -->
    <div id="productModal" class="modal">
        <div class="auth-card" style="max-width: 600px; max-height: 90vh; overflow-y: auto; position: relative;">
            <button class="badge" onclick="closeModal('productModal')" style="position: absolute; top: 15px; right: 15px; background: #ef4444; border: none; cursor: pointer; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; z-index: 10;">&times;</button>
            <div class="auth-header" style="text-align: left;">
                <h2 style="font-size: 20px;">Product Details</h2>
                <p>Register a new product in the warehouse inventory.</p>
            </div>
            <form id="addProductForm" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" class="form-input" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" id="nameInput" class="form-input" required onblur="syncProductByName(this.value)">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" id="categorySelect" class="form-input" required onchange="loadSubCategoryOptions(this.value)">
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sub-Category</label>
                        <select name="sub_category_id" id="subCategorySelect" class="form-input">
                            <option value="">Select Sub-Category</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; justify-content: space-between;">
                            Serial Number 
                            <span onclick="generateAutoSN()" style="font-size: 10px; color: var(--primary); cursor: pointer; text-decoration: underline;">Auto-Gen</span>
                        </label>
                        <input type="text" name="serial_number" id="serialInput" class="form-input" placeholder="Unique Serial">
                    </div>
                    <div class="form-group">
                        <label style="display: flex; justify-content: space-between;">
                            Barcode 
                            <span onclick="generateAutoBarcode()" style="font-size: 10px; color: var(--primary); cursor: pointer; text-decoration: underline;">Auto-Gen</span>
                        </label>
                        <input type="text" name="barcode" id="barcodeInput" class="form-input" placeholder="Unique Barcode">
                    </div>
                    <div class="form-group">
                        <label>Purchase Price (Buy) - <?php echo CURRENCY_SYMBOL; ?></label>
                        <input type="number" step="0.01" name="purchase_price" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Retail Price (Sell) - <?php echo CURRENCY_SYMBOL; ?></label>
                        <input type="number" step="0.01" name="price" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Initial Stock</label>
                        <input type="number" step="0.001" name="stock_quantity" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Min. Reorder Level</label>
                        <input type="number" name="reorder_level" class="form-input" value="5" required title="Alert will trigger when stock hits this level">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number" class="form-input" placeholder="e.g. B-204">
                    </div>
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>HSN Code (GST)</label>
                        <input type="text" name="hsn_code" class="form-input" placeholder="8-digit code">
                    </div>
                    <div class="form-group">
                        <label>Storage Warehouse</label>
                        <select name="warehouse_id" class="form-input">
                            <?php
                            $warehouses = $conn->query("SELECT id, name FROM warehouses");
                            while($wh = $warehouses->fetch_assoc()) {
                                echo "<option value='{$wh['id']}'>{$wh['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rack Location (A1-E2)</label>
                        <input type="text" name="rack_location" class="form-input" placeholder="e.g. A1" maxlength="5" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label>Bin Number (001-004)</label>
                        <input type="text" name="bin_location" class="form-input" placeholder="e.g. 001" maxlength="5">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-input" style="height: 60px;"></textarea>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="closeModal('productModal')">Cancel</button>
                    <button type="submit" class="btn">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Barcode Label Modal -->
    <div id="labelModal" class="modal">
        <div class="auth-card" style="max-width: 400px; text-align: center;">
            <h2 id="labelTitle">Print Label</h2>
            <div id="labelContainer" style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <svg id="barcodeSVG"></svg>
                <div id="labelPrice" style="color: black; font-weight: 700; margin-top: 10px;"></div>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn" style="background: var(--secondary);" onclick="closeModal('labelModal')">Close</button>
                <button type="button" class="btn" onclick="printLabel()">Print Label</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Handlers
        function openModal(id) {
            if(id === 'productModal') {
                const form = document.getElementById('addProductForm');
                form.reset();
                document.getElementById('subCategorySelect').innerHTML = '<option value="">Select Sub-Category</option>';
                if(form.elements['id']) form.elements['id'].remove();
                document.querySelector('#productModal h2').innerText = 'Product Details';
                loadCategoryOptions();
                
                // Auto-generate on open for new products
                generateAutoSN();
                generateAutoBarcode();
            }
            document.getElementById(id).style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Auto Generation Logic
        function generateAutoSN() {
            const prefix = "SN" + Math.floor(Math.random() * 90 + 10);
            const ts = Date.now().toString().slice(-6);
            const rand = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            document.getElementById('serialInput').value = `${prefix}-${ts}${rand}`;
        }

        function generateAutoBarcode() {
            // Generate a 12 digit number
            let barcode = "";
            for(let i=0; i<12; i++) {
                barcode += Math.floor(Math.random() * 10);
            }
            document.getElementById('barcodeInput').value = barcode;
        }

        function syncProductByName(name) {
            if(!name || name.trim() === "") return;
            // Only sync if we are in "Add" mode (not editing)
            const form = document.getElementById('addProductForm');
            if(form.elements['id'] && form.elements['id'].value > 0) return;

            fetch(`../../actions/get_product_by_name.php?name=${encodeURIComponent(name)}`)
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const p = data.product;
                        // Overwrite Serial/Barcode with existing product data (Sync)
                        form.elements['serial_number'].value = p.serial_number;
                        form.elements['barcode'].value = p.barcode;
                        // Sync Other details for convenience
                        form.elements['category_id'].value = p.category_id;
                        loadSubCategoryOptions(p.category_id, p.sub_category_id);
                        form.elements['purchase_price'].value = p.purchase_price;
                        form.elements['price'].value = p.price;
                        form.elements['hsn_code'].value = p.hsn_code;
                        form.elements['reorder_level'].value = p.reorder_level;
                        form.elements['description'].value = p.description;
                    }
                });
        }

        // Fetch categories for dropdown
        function loadCategoryOptions() {
            fetch('../../actions/get_categories.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('categorySelect');
                    select.innerHTML = '<option value="">Select Category</option>';
                    data.forEach(cat => {
                        select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                    });
                });
        }

        function loadSubCategoryOptions(categoryId, selectedSubId = null) {
            const subSelect = document.getElementById('subCategorySelect');
            if(!categoryId) {
                subSelect.innerHTML = '<option value="">Select Sub-Category</option>';
                return;
            }
            fetch(`../../actions/get_sub_categories.php?category_id=${categoryId}`)
                .then(res => res.json())
                .then(data => {
                    subSelect.innerHTML = '<option value="">Select Sub-Category</option>';
                    data.forEach(sub => {
                        const selected = (selectedSubId && sub.id == selectedSubId) ? 'selected' : '';
                        subSelect.innerHTML += `<option value="${sub.id}" ${selected}>${sub.name}</option>`;
                    });
                });
        }

        // Main data loader
        function loadProducts() {
            fetch('../../actions/get_products.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('productTableBody');
                    tbody.innerHTML = '';
                    data.forEach(p => {
                        const img = p.image_path ? `../../${p.image_path}` : 'https://placehold.co/50x50/1e293b/f8fafc?text=📦';
                        tbody.innerHTML += `
                            <tr style="border-top: 1px solid var(--border-color);">
                                <td style="padding: 16px 24px;">
                                    <img src="${img}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);">
                                </td>
                                <td style="padding: 16px 24px;">
                                    <div style="font-size: 14px; font-weight: 600;">${p.barcode || 'N/A'}</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">${p.serial_number || 'N/A'}</div>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <div style="font-weight: 500;">${p.name}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        <strong>${p.category_name || 'N/A'}</strong> 
                                        ${p.sub_category_name ? ' > ' + p.sub_category_name : ''}
                                    </div>
                                </td>
                                <td style="padding: 16px 24px;"><span class="badge ${p.stock_quantity < 5 ? 'badge-danger' : 'badge-success'}">${parseFloat(p.stock_quantity).toFixed(2)} units</span></td>
                                <td style="padding: 16px 24px;">
                                    <div style="font-size: 12px; font-weight: 700; color: var(--primary);">Rack: ${p.rack_location || 'A1'}</div>
                                    <div style="font-size: 10px; color: var(--text-muted);">Bin: ${p.bin_location || '001'}</div>
                                </td>
                                <td style="padding: 16px 24px;">${window.CURRENCY_SYMBOL}${p.purchase_price}</td>
                                <td style="padding: 16px 24px; font-weight: 600;">${window.CURRENCY_SYMBOL}${p.price}</td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <button onclick="generateLabel('${p.barcode}', '${p.name}', '${p.price}')" title="Generate Label" style="background: none; border: none; color: var(--success); cursor: pointer;"><i class="fas fa-barcode"></i></button>
                                    <button onclick="editProduct(${JSON.stringify(p).replace(/"/g, '&quot;')})" style="background: none; border: none; color: var(--primary); cursor: pointer; margin-left:10px;"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteProduct(${p.id})" style="background: none; border: none; color: var(--danger); cursor: pointer; margin-left: 10px;"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        function exportCSV() {
            window.location.href = '../../actions/export_csv.php?type=products';
        }

        // Barcode Label Generation
        function generateLabel(code, name, price) {
            if(!code || code === 'null' || code === '') { alert('Please assign a barcode to this product first.'); return; }
            openModal('labelModal');
            document.getElementById('labelPrice').innerText = `${name} - ${window.CURRENCY_SYMBOL}${price}`;
            JsBarcode("#barcodeSVG", code, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 80,
                displayValue: true
            });
        }

        function printLabel() {
            const printWindow = window.open('', '', 'width=600,height=600');
            printWindow.document.write('<html><head><title>Print Label</title><style>body{display:flex;flex-direction:column;align-items:center;padding:20px;font-family:sans-serif;}</style></head><body>');
            printWindow.document.write(document.getElementById('labelContainer').innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
        }

        // Fill form for editing
        function editProduct(p) {
            openModal('productModal');
            const form = document.getElementById('addProductForm');
            form.elements['name'].value = p.name;
            form.elements['category_id'].value = p.category_id;
            loadSubCategoryOptions(p.category_id, p.sub_category_id);
            form.elements['serial_number'].value = p.serial_number;
            form.elements['barcode'].value = p.barcode;
            form.elements['purchase_price'].value = p.purchase_price;
            form.elements['price'].value = p.price;
            form.elements['stock_quantity'].value = p.stock_quantity;
            form.elements['description'].value = p.description;
            form.elements['rack_location'].value = p.rack_location || 'A1';
            form.elements['bin_location'].value = p.bin_location || '001';
            
            if(!form.elements['id']) {
                const hid = document.createElement('input');
                hid.type = 'hidden'; hid.name = 'id';
                form.appendChild(hid);
            }
            form.elements['id'].value = p.id;
            document.querySelector('#productModal h2').innerText = 'Edit Product';
        }

        function deleteProduct(id) {
            if(!confirm('Are you sure you want to remove this product from inventory?')) return;
            fetch('../../actions/delete_product.php?id=' + id).then(res => res.json()).then(data => {
                if(data.success) loadProducts(); else alert(data.message);
            });
        }

        // Handle Add/Edit form submission
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('../../actions/add_product.php', { method: 'POST', body: new FormData(this) })
            .then(res => res.json()).then(data => {
                if(data.success) { 
                    closeModal('productModal'); 
                    loadProducts(); 
                    this.reset();
                    if(this.elements['id']) this.elements['id'].remove();
                    document.querySelector('#productModal h2').innerText = 'Product Details';
                }
                else alert(data.message);
            });
        });

        loadProducts();
    </script>
</body>
</html>
