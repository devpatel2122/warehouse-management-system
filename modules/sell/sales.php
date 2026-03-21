<?php
/**
 * Warehouse Pro - Sales Transaction Terminal (POS)
 * Module: Sell/Sales
 */
$page_title = 'Sales';
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
    <title><?php echo $page_title; ?> | Warehouse System</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
                    <h1 style="font-size: 24px; font-weight: 700;">Complete a Sale</h1>
                    <p style="color: var(--text-muted);">Manage sales, customers, and generate invoices.</p>
                </div>
            </header>

            <div style="display: grid; grid-template-columns: 1fr 350px; gap: 24px;">
                <!-- Item Selection Area -->
                <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
                    <div class="form-group" style="position: relative;">
                        <label>Search Product (Name or Barcode)</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="productSearch" class="form-input" placeholder="Scan or type product name...">
                            <button id="cameraBtn" class="btn" style="width: auto; background: var(--secondary);" title="Use Mobile Camera to Scan Barcode"><i class="fas fa-camera"></i></button>
                        </div>
                        <div id="qr-reader" style="width: 100%; margin-top: 10px; display: none; border-radius: 12px; overflow: hidden; border: 2px solid var(--primary);"></div>
                        <div id="searchResults" style="margin-top: 8px; max-height: 200px; overflow-y: auto; background: var(--bg-dark); border-radius: 8px; display: none; border: 1px solid var(--border-color); z-index: 10;"></div>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 24px;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 12px; color: var(--text-muted);">Item</th>
                                <th style="padding: 12px; color: var(--text-muted);">Price</th>
                                <th style="padding: 12px; color: var(--text-muted);">Qty</th>
                                <th style="padding: 12px; color: var(--text-muted);">Total</th>
                                <th style="padding: 12px; color: var(--text-muted);">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <!-- Items populated via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Transaction Summary -->
                <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); height: fit-content;">
                    <h3 style="margin-bottom: 20px;">Checkout</h3>
                    <div class="form-group">
                        <label>Customer Assignment</label>
                        <select id="customerSelect" class="form-input">
                            <option value="">Walk-in Customer</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label>Transaction Date</label>
                        <input type="text" id="saleDate" class="form-input" value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label>Payment Method</label>
                        <select id="paymentMethod" class="form-input" onchange="syncPaymentStatus()">
                            <option value="Cash">Cash Payment</option>
                            <option value="UPI">UPI / QR Scan</option>
                            <option value="Card">Card Swipe</option>
                            <option value="Credit">Store Credit (Unpaid)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label>Initial status</label>
                        <select id="paymentStatus" class="form-input">
                            <option value="Paid">Mark as Paid</option>
                            <option value="Unpaid">Mark as Unpaid</option>
                        </select>
                    </div>
                    
                    <div style="margin: 24px 0; border-top: 1px solid var(--border-color); padding-top: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: var(--text-muted);">Subtotal</span>
                            <span id="subtotal"><?php echo CURRENCY_SYMBOL; ?>0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: var(--text-muted);">Tax Breakdown</span>
                            <span id="taxAmount"><?php echo CURRENCY_SYMBOL; ?>0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 18px; color: var(--primary);">
                            <span>Grand Total</span>
                            <span id="grandTotal"><?php echo CURRENCY_SYMBOL; ?>0.00</span>
                        </div>
                    </div>

                    <button class="btn" id="completeSaleBtn">Finalize Transaction</button>
                </div>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let cart = [];
        const searchInput = document.getElementById('productSearch');
        const resultsDiv = document.getElementById('searchResults');
        const cgst_rate = <?php echo $settings['cgst_rate'] ?? 0; ?>;
        const sgst_rate = <?php echo $settings['sgst_rate'] ?? 0; ?>;

        // Search logic
        searchInput.addEventListener('input', function(e) {
            searchProducts(e.target.value);
        });

        function searchProducts(query) {
            if (query.length < 2) {
                resultsDiv.style.display = 'none';
                return;
            }
            fetch('../../actions/search_products.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        // Quick Add: If it's a barcode scan and only 1 exact match (length 1), add it instantly
                        if (data.length === 1 && (query === data[0].barcode || query === data[0].serial_number)) {
                            addToCart(data[0]);
                            resultsDiv.style.display = 'none';
                            return;
                        }

                        resultsDiv.style.display = 'block';
                        data.forEach(p => {
                            const pJson = JSON.stringify(p).replace(/'/g, "&apos;");
                            resultsDiv.innerHTML += `
                                <div class="search-item" onclick='addToCart(${pJson})' style="padding:12px; cursor:pointer; border-bottom:1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                                    <div>
                                        <strong style="color: var(--text-main);">${p.name}</strong><br>
                                        <small style="color: var(--text-muted);">SN: ${p.serial_number || 'N/A'} | Barcode: ${p.barcode || 'N/A'}</small>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 700; color: var(--primary);">${CURRENCY_SYMBOL}${p.price}</div>
                                        <div style="font-size: 10px; color: ${p.stock_quantity > 5 ? 'var(--success)' : 'var(--danger)'};">Stock: ${p.stock_quantity}</div>
                                    </div>
                                </div>`;
                        });
                    } else {
                        resultsDiv.style.display = 'none';
                    }
                });
        }

        function addToCart(p) {
            const existing = cart.find(item => item.id == p.id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({...p, qty: 1});
            }
            resultsDiv.style.display = 'none';
            searchInput.value = '';
            updateCartUI();
        }

        function updateCartUI() {
            const tbody = document.getElementById('cartItems');
            tbody.innerHTML = '';
            cart.forEach(item => {
                const itemTotal = item.price * item.qty;
                tbody.innerHTML += `
                    <tr id="cart-row-${item.id}">
                        <td style="padding:12px;">${item.name}</td>
                        <td style="padding:12px;">${CURRENCY_SYMBOL}${item.price}</td>
                        <td style="padding:12px;">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <button type="button" onclick="adjustQty(${item.id}, -1)" style="width:30px; height:30px; border-radius:4px; border:1px solid var(--border-color); background:var(--bg-dark); color:var(--text-main); cursor:pointer; font-weight:bold;">-</button>
                                <input type="number" id="qty-input-${item.id}" value="${item.qty}" min="1" 
                                    oninput="updateQty(${item.id}, this.value)"
                                    style="width:60px; text-align:center; background:var(--bg-dark); border:1px solid var(--border-color); color:var(--text-main); border-radius:4px; padding:6px; font-weight:600;">
                                <button type="button" onclick="adjustQty(${item.id}, 1)" style="width:30px; height:30px; border-radius:4px; border:1px solid var(--border-color); background:var(--bg-dark); color:var(--text-main); cursor:pointer; font-weight:bold;">+</button>
                            </div>
                        </td>
                        <td id="total-price-${item.id}" style="padding:12px;">${CURRENCY_SYMBOL}${itemTotal.toFixed(2)}</td>
                        <td style="padding:12px;"><button type="button" onclick="removeFromCart(${item.id})" class="text-danger" style="background:none; border:none; cursor:pointer;"><i class="fas fa-trash"></i></button></td>
                    </tr>`;
            });
            calculateTotals();
        }

        function calculateTotals() {
            let total = 0;
            cart.forEach(item => {
                total += (parseFloat(item.price) * (parseInt(item.qty) || 0));
            });

            // Calculate GST
            const subtotal = parseFloat(total) || 0;
            const tax = subtotal * ((parseFloat(cgst_rate) + parseFloat(sgst_rate)) / 100);
            
            document.getElementById('subtotal').innerText = CURRENCY_SYMBOL + subtotal.toFixed(2);
            document.getElementById('taxAmount').innerText = CURRENCY_SYMBOL + tax.toFixed(2);
            document.getElementById('grandTotal').innerText = CURRENCY_SYMBOL + (subtotal + tax).toFixed(2);
        }

        function adjustQty(id, delta) {
            const item = cart.find(i => i.id == id);
            if(item) {
                const newQty = Math.max(1, (parseInt(item.qty) || 0) + delta);
                updateQty(id, newQty, true); // true means refresh the input value too
            }
        }

        function updateQty(id, qty, refreshInput = false) {
            const item = cart.find(i => i.id == id);
            if(item) {
                item.qty = Math.max(1, parseInt(qty) || 1);
                
                // Update specific elements instead of re-rendering whole table
                const totalCell = document.getElementById(`total-price-${id}`);
                if(totalCell) {
                    totalCell.innerText = CURRENCY_SYMBOL + (item.price * item.qty).toFixed(2);
                }
                
                if(refreshInput) {
                    const input = document.getElementById(`qty-input-${id}`);
                    if(input) input.value = item.qty;
                }
                
                calculateTotals();
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(i => i.id != id);
            updateCartUI();
        }

        // Camera Scanner Logic
        const html5QrCode = new Html5Qrcode("qr-reader");
        let scannerRunning = false;

        document.getElementById('cameraBtn').addEventListener('click', () => {
            const reader = document.getElementById('qr-reader');
            if (!scannerRunning) {
                reader.style.display = 'block';
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    { fps: 15, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        searchInput.value = decodedText;
                        searchProducts(decodedText);
                        stopScanner();
                    },
                    (err) => {}
                ).then(() => {
                    scannerRunning = true;
                    document.getElementById('cameraBtn').innerHTML = '<i class="fas fa-stop"></i>';
                    document.getElementById('cameraBtn').style.background = 'var(--danger)';
                }).catch(err => {
                    alert('Camera access denied or error: ' + err);
                });
            } else {
                stopScanner();
            }
        });

        function stopScanner() {
            html5QrCode.stop().then(() => {
                document.getElementById('qr-reader').style.display = 'none';
                scannerRunning = false;
                document.getElementById('cameraBtn').innerHTML = '<i class="fas fa-camera"></i>';
                document.getElementById('cameraBtn').style.background = 'var(--secondary)';
            });
        }

        // Finalize Sale
        document.getElementById('completeSaleBtn').addEventListener('click', () => {
            if(cart.length === 0) return alert('Cart is empty!');
            
            let finalTotal = 0;
            cart.forEach(i => finalTotal += (parseFloat(i.price) * parseInt(i.qty)));
            finalTotal = finalTotal * (1 + (cgst_rate + sgst_rate)/100);

            const data = {
                customer_id: document.getElementById('customerSelect').value,
                payment_method: document.getElementById('paymentMethod').value,
                payment_status: document.getElementById('paymentStatus').value,
                sale_date: document.getElementById('saleDate').value,
                items: cart,
                total: finalTotal.toFixed(2)
            };
            
            fetch('../../actions/process_sale.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    if (data.payment_method === 'UPI') {
                        if(confirm('Sale Recorded (Unpaid). Would you like to open the QR Code for immediate collection?')) {
                            window.location.href = 'invoices.php?pay_now=' + res.sale_id;
                            return;
                        }
                    } else {
                        alert('Sale Complete! Invoice generated successfully.');
                    }
                    
                    if(confirm("Do you want to see all invoices?")) {
                        window.location.href = 'invoices.php';
                    }
                    cart = [];
                    updateCartUI();
                } else alert('Error: ' + res.message);
            });
        });

        function syncPaymentStatus() {
            const method = document.getElementById('paymentMethod').value;
            const statusBox = document.getElementById('paymentStatus');
            
            // Logic: Credit and UPI usually start as Unpaid, Cash/Card as Paid
            if (method === 'Credit' || method === 'UPI') {
                statusBox.value = 'Unpaid';
            } else {
                statusBox.value = 'Paid';
            }
        }

        // Load Customers
        fetch('../../actions/get_customers.php')
            .then(res => res.json())
            .then(data => {
                const sel = document.getElementById('customerSelect');
                data.forEach(c => {
                    sel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
                });
            });

        // Initialize Date Picker for POS
        flatpickr("#saleDate", {
            defaultDate: "today",
            dateFormat: "Y-m-d",
            theme: "dark"
        });
    </script>
</body>
</html>
