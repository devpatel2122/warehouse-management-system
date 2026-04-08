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
                            <input type="text" id="productSearch" class="form-input" placeholder="Scan or type product name..." autocomplete="off">
                            <button id="cameraBtn" class="btn" style="width: auto; background: var(--secondary);" title="Use Mobile Camera to Scan Barcode"><i class="fas fa-camera"></i></button>
                        </div>
                        <div id="qr-reader" style="width: 100%; margin-top: 10px; display: none; border-radius: 12px; overflow: hidden; border: 2px solid var(--primary);"></div>
                        <div id="searchResults" style="position: absolute; top: calc(100% + 5px); left: 0; right: 0; max-height: 250px; overflow-y: auto; background: var(--card-bg); border-radius: 12px; display: none; border: 1px solid var(--border-color); z-index: 9999; box-shadow: 0 10px 25px rgba(0,0,0,0.5);"></div>
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
                        <select id="customerSelect" class="form-input" onchange="toggleWalkinInfo()">
                            <option value="">Walk-in Customer</option>
                        </select>
                    </div>

                    <!-- Walk-in Customer Info -->
                    <div id="walkinInfo" style="margin-top: 15px; background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 12px; border: 1px dashed var(--border-color);">
                        <p style="font-size: 11px; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; gap: 5px;">
                            <i class="fas fa-user-plus"></i> Walk-in Details (Optional)
                        </p>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <input type="text" id="walkin_name" class="form-input" placeholder="Guest Name">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <input type="text" id="walkin_contact" class="form-input" placeholder="Mobile / Contact">
                        </div>
                        <div class="form-group">
                            <input type="text" id="walkin_address" class="form-input" placeholder="Area / City (Optional)">
                        </div>
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
                            <option value="Card">Card Swipe / Online</option>
                            <option value="Credit">Store Credit (Unpaid)</option>
                        </select>
                    </div>

                    <div id="refField" class="form-group" style="margin-top: 15px; display: none;">
                        <label>Reference # (Auth Code / UPI ID)</label>
                        <input type="text" id="transactionRef" class="form-input" placeholder="e.g. 123456">
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

    <!-- Success Modal -->
    <div id="successModal" class="modal" style="display: none; align-items: center; justify-content: center; z-index: 2000;">
        <div class="auth-card" style="max-width: 400px; text-align: center; background: var(--bg-dark); border: 1px solid var(--primary); padding: 40px; border-radius: 24px;">
            <div style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 40px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 style="margin-bottom: 10px;">Transaction Success!</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 30px;">The sale has been recorded and inventory updated.</p>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <button id="printReceiptBtn" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 700;">
                    <i class="fas fa-print"></i> Print Receipt / Bill
                </button>
                <a href="invoices.php" class="btn" style="background: var(--secondary); text-decoration: none; display: block; line-height: 48px; border-radius: 12px;">
                    <i class="fas fa-list"></i> View All Invoices
                </a>
                <button onclick="document.getElementById('successModal').style.display='none'" class="btn" style="background: transparent; color: var(--text-muted); border: 1px solid var(--border-color);">
                    New Transaction
                </button>
            </div>
        </div>
    </div>

    <!-- Card Terminal Modal (The "Mobile Machine") -->
    <div id="cardMachineModal" class="modal" style="display: none; align-items: center; justify-content: center; z-index: 3000;">
        <div style="background: #111827; width: 320px; border-radius: 40px; padding: 30px; border: 8px solid #374151; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); position: relative;">
            <div style="width: 100%; height: 20px; background: #000; border-radius: 5px; margin-bottom: 20px;"></div>
            <div id="terminalScreen" style="background: #064e3b; height: 160px; border-radius: 12px; margin-bottom: 25px; padding: 20px; font-family: monospace; color: #10b981; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                <div id="terminalStatus">INSERT/SWIPE CARD</div>
                <div id="terminalAmount" style="font-size: 24px; font-weight: bold; margin-top: 10px;"></div>
                <div id="terminalLoader" style="display: none; margin-top: 15px;">
                    <i class="fas fa-spinner fa-spin"></i> PROCESSING...
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px;">
                <div style="background: #1f2937; padding: 12px; text-align: center; border-radius: 8px; color: #94a3b8;">1</div>
                <div style="background: #1f2937; padding: 12px; text-align: center; border-radius: 8px; color: #94a3b8;">2</div>
                <div style="background: #1f2937; padding: 12px; text-align: center; border-radius: 8px; color: #94a3b8;">3</div>
                <div style="background: #1f2937; padding: 12px; text-align: center; border-radius: 8px; color: #94a3b8;">4</div>
                <div style="background: #1f2937; padding: 12px; text-align: center; border-radius: 8px; color: #94a3b8;">5</div>
                <div style="background: #1f2937; padding: 12px; text-align: center; border-radius: 8px; color: #94a3b8;">6</div>
                <div style="background: #ef4444; padding: 12px; text-align: center; border-radius: 8px; color: white; cursor: pointer;" onclick="document.getElementById('cardMachineModal').style.display='none'">X</div>
                <div style="background: #1f2937; padding: 12px; text-align: center; border-radius: 8px; color: #94a3b8;">0</div>
                <div id="confirmSweepBtn" style="background: #10b981; padding: 12px; text-align: center; border-radius: 8px; color: white; cursor: pointer;">OK</div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        // Use window.CURR to avoid redeclaration issues across script blocks
        window.CURR = '<?php echo CURRENCY_SYMBOL; ?>';
        const CGST = parseFloat('<?php echo $settings['cgst_rate'] ?? 0; ?>') || 0;
        const SGST = parseFloat('<?php echo $settings['sgst_rate'] ?? 0; ?>') || 0;
        
        let cart = [];
        let lastSearchResults = [];
        const searchInput = document.getElementById('productSearch');
        const resultsDiv = document.getElementById('searchResults');

        function calculateTotals() {
            let total = 0;
            cart.forEach(item => { total += (parseFloat(item.price) * (parseInt(item.qty) || 0)); });
            const subtotal = total;
            const tax = subtotal * ((CGST + SGST) / 100);
            const grandTotal = subtotal + tax;
            if(document.getElementById('subtotal')) document.getElementById('subtotal').innerText = window.CURR + subtotal.toFixed(2);
            if(document.getElementById('taxAmount')) document.getElementById('taxAmount').innerText = window.CURR + tax.toFixed(2);
            if(document.getElementById('grandTotal')) document.getElementById('grandTotal').innerText = window.CURR + grandTotal.toFixed(2);
            return grandTotal;
        }

        function updateCartUI() {
            const tbody = document.getElementById('cartItems');
            if(!tbody) return;
            tbody.innerHTML = '';
            cart.forEach(item => {
                const itemTotal = item.price * item.qty;
                tbody.innerHTML += `<tr>
                    <td style="padding:12px;">${item.name}</td>
                    <td style="padding:12px;">${window.CURR}${item.price}</td>
                    <td style="padding:12px;">
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <button type="button" onclick="adjustQty(${item.id}, -1)" class="qty-btn">-</button>
                            <input type="number" value="${item.qty}" min="1" oninput="updateQty(${item.id}, this.value)" class="qty-input">
                            <button type="button" onclick="adjustQty(${item.id}, 1)" class="qty-btn">+</button>
                        </div>
                    </td>
                    <td style="padding:12px;">${window.CURR}${itemTotal.toFixed(2)}</td>
                    <td style="padding:12px;"><button type="button" onclick="removeFromCart(${item.id})" class="text-danger" style="background:none; border:none; cursor:pointer;"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
            calculateTotals();
        }

        function adjustQty(id, delta) {
            const item = cart.find(i => i.id == id);
            if(item) { item.qty = Math.max(1, (parseInt(item.qty) || 1) + delta); updateCartUI(); }
        }

        function updateQty(id, qty) {
            const item = cart.find(i => i.id == id);
            if(item) { item.qty = Math.max(1, parseInt(qty) || 1); calculateTotals(); }
        }

        function removeFromCart(id) { cart = cart.filter(i => i.id != id); updateCartUI(); }

        function addToCart(p) {
            const existing = cart.find(item => item.id == p.id);
            if (existing) { existing.qty++; } else { cart.push({...p, qty: 1}); }
            resultsDiv.style.display = 'none';
            searchInput.value = '';
            updateCartUI();
        }

        function addToCartById(id) {
            const product = lastSearchResults.find(p => p.id == id);
            if (product) addToCart(product);
        }

        function searchProducts(query) {
            if (query.length < 2) { resultsDiv.style.display = 'none'; return; }
            fetch('../../actions/search_products.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    lastSearchResults = data;
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        if (data.length === 1 && (query === data[0].barcode || query === data[0].serial_number)) {
                            addToCart(data[0]);
                            resultsDiv.style.display = 'none';
                            return;
                        }
                        resultsDiv.style.display = 'block';
                        data.forEach(p => {
                            resultsDiv.innerHTML += `<div class="search-item" onclick="addToCartById(${p.id})" style="padding:15px; cursor:pointer; border-bottom:1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                                <div><div style="font-weight: 700; color: var(--text-main); font-size: 14px;">${p.name}</div><div style="font-size: 11px; color: var(--text-muted);">${p.category_name || 'Generic'} • SN: ${p.serial_number || 'N/A'}</div></div>
                                <div style="text-align: right;"><div style="font-weight: 800; color: var(--primary); font-size: 15px;">${window.CURR}${p.price}</div><div style="font-size: 10px; font-weight: 600; color: ${p.stock_quantity > 0 ? '#10b981' : '#ef4444'}; text-transform: uppercase;">Stock: ${parseFloat(p.stock_quantity).toFixed(0)}</div></div>
                            </div>`;
                        });
                    } else { resultsDiv.style.display = 'none'; }
                });
        }

        function performSale() {
            if(cart.length === 0) return alert('Cart is empty!');
            const data = {
                customer_id: document.getElementById('customerSelect').value,
                walkin_name: document.getElementById('walkin_name').value, 
                walkin_contact: document.getElementById('walkin_contact').value, 
                walkin_address: document.getElementById('walkin_address').value,
                payment_method: document.getElementById('paymentMethod').value, 
                payment_status: document.getElementById('paymentStatus').value,
                sale_date: document.getElementById('saleDate').value, 
                items: cart, 
                total: calculateTotals().toFixed(2), 
                transaction_ref: document.getElementById('transactionRef').value
            };
            fetch('../../actions/process_sale.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) })
            .then(res => res.json()).then(res => {
                if(res.success) {
                    const sm = document.getElementById('successModal'); sm.style.display = 'flex';
                    document.getElementById('printReceiptBtn').onclick = () => printReceipt(res.sale_id);
                    cart = []; updateCartUI();
                    ['walkin_name','walkin_contact','walkin_address','transactionRef'].forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
                } else alert('Sale failed: ' + res.message);
            }).catch(e => alert('Connection Error.'));
        }

        document.getElementById('completeSaleBtn').addEventListener('click', () => {
            const method = document.getElementById('paymentMethod').value;
            const total = calculateTotals();
            if (method === 'Card') {
                const modal = document.getElementById('cardMachineModal');
                document.getElementById('terminalAmount').innerText = window.CURR + total.toFixed(2);
                document.getElementById('terminalStatus').innerText = 'INSERT/SWIPE CARD';
                document.getElementById('terminalLoader').style.display = 'none';
                modal.style.display = 'flex';
                document.getElementById('confirmSweepBtn').onclick = () => {
                    document.getElementById('terminalStatus').innerText = 'COMMUNICATING...';
                    document.getElementById('terminalLoader').style.display = 'block';
                    setTimeout(() => {
                        const auth = 'AUTH-' + Math.floor(Math.random() * 900000 + 100000);
                        document.getElementById('terminalStatus').innerText = 'APPROVED';
                        document.getElementById('terminalLoader').style.display = 'none';
                        document.getElementById('transactionRef').value = auth;
                        setTimeout(() => { modal.style.display = 'none'; performSale(); }, 800);
                    }, 1800);
                };
            } else { performSale(); }
        });

        searchInput.addEventListener('input', (e) => searchProducts(e.target.value));

        function printReceipt(id) {
            fetch('../../actions/get_sale_details.php?id=' + id).then(r => r.json()).then(data => {
                if(!data.success) return alert('Failed to fetch invoice.');
                const { sale, items } = data;
                const companyName = '<?php echo APP_NAME; ?>';
                const printWindow = window.open('', '', 'width=900,height=800');
                let itemsHtml = '';
                items.forEach(item => { itemsHtml += `<tr><td>${item.product_name}</td><td>${window.CURR}${item.unit_price}</td><td>${item.quantity}</td><td>${window.CURR}${(item.unit_price * item.quantity).toFixed(2)}</td></tr>`; });
                printWindow.document.write(`<html><head><title>Print Receipt</title><style>body { font-family: 'Inter', sans-serif; color: #1e293b; padding: 20px; } .invoice-box { max-width: 800px; margin: auto; border: 1px solid #e2e8f0; padding: 40px; border-radius: 12px; } .header { display: flex; justify-content: space-between; margin-bottom: 40px; } table { width: 100%; border-collapse: collapse; } th { background: #f8fafc; text-align: left; padding: 12px; } td { padding: 12px; border-bottom: 1px solid #e2e8f0; } .grand-total { font-weight: 800; color: #6366f1; font-size: 20px; }</style></head><body>
                    <div class="invoice-box">
                        <div class="header"><div><h1>${companyName}</h1></div><div style="text-align: right;"><h2>INVOICE</h2><p>#${sale.invoice_no}</p></div></div>
                        <div><strong>Bill To:</strong><br>${sale.customer_name || sale.walkin_name || 'Walk-in Customer'}</div>
                        <table><thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead><tbody>${itemsHtml}</tbody></table>
                        <div style="float: right; width: 300px; margin-top: 30px;"><div style="display: flex; justify-content: space-between;"><span>Subtotal</span><span>${window.CURR}${(sale.total_amount - (parseFloat(sale.cgst_amount) + parseFloat(sale.sgst_amount))).toFixed(2)}</span></div><div class="grand-total" style="display: flex; justify-content: space-between;"><span>Total</span><span>${window.CURR}${sale.total_amount}</span></div></div>
                    </div><script>window.onload = function() { window.print(); }<\/script></body></html>`);
                printWindow.document.close();
            });
        }

        function toggleWalkinInfo() { 
            const val = document.getElementById('customerSelect').value; 
            document.getElementById('walkinInfo').style.display = (val === '') ? 'block' : 'none'; 
        }

        function syncPaymentStatus() {
            const m = document.getElementById('paymentMethod').value; 
            const s = document.getElementById('paymentStatus'); 
            const f = document.getElementById('refField');
            if (m === 'Card' || m === 'UPI') { f.style.display = 'block'; s.value = (m === 'Card') ? 'Paid' : 'Unpaid'; } 
            else { f.style.display = 'none'; s.value = (m === 'Cash') ? 'Paid' : 'Unpaid'; }
        }

        let html5QrcodeScanner = null;

        document.getElementById('cameraBtn').addEventListener('click', () => {
            const qrDiv = document.getElementById('qr-reader');
            if (qrDiv.style.display === 'block') {
                // If already open, close it
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().catch(err => console.error(err));
                    html5QrcodeScanner = null;
                }
                qrDiv.style.display = 'none';
            } else {
                // Open and start scanner
                qrDiv.style.display = 'block';
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", 
                    { fps: 10, qrbox: {width: 250, height: 250} }, 
                    false
                );
                
                html5QrcodeScanner.render((decodedText, decodedResult) => {
                    // On success
                    searchInput.value = decodedText;
                    searchProducts(decodedText);
                    
                    // Auto-close scanner after successful scan
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.clear().catch(err => console.error(err));
                        html5QrcodeScanner = null;
                    }
                    qrDiv.style.display = 'none';
                }, (error) => {
                    // Ignore continuous scan errors (normal behavior until a code is found)
                });
            }
        });

        // Init
        fetch('../../actions/get_customers.php').then(r => r.json()).then(data => { 
            const sel = document.getElementById('customerSelect'); 
            data.forEach(c => { sel.innerHTML += `<option value="${c.id}">${c.name}</option>`; }); 
        });
        flatpickr("#saleDate", { defaultDate: "today", dateFormat: "Y-m-d", theme: "dark" });
    </script>
    <style>
        .qty-btn { width:30px; height:30px; border-radius:4px; border:1px solid var(--border-color); background:var(--bg-dark); color:var(--text-main); cursor:pointer; }
        .qty-input { width:50px; text-align:center; background:var(--bg-dark); border:1px solid var(--border-color); color:var(--text-main); border-radius:4px; padding:6px; }
    </style>
</body>
</html>
