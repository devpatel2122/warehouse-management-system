<?php
$page_title = 'Sales Invoices';
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
    <title>Manage Invoices | Warehouse System v2</title>
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
            <header class="mb-4">
                <h1 style="font-size: 24px; font-weight: 700;">Sales Invoices</h1>
                <p style="color: var(--text-muted);">View and manage all customer transaction records.</p>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceTableBody">
                        <!-- Ajax data -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- QR Payment Modal -->
    <div id="paymentModal" class="modal" style="display: none; align-items: center; justify-content: center;">
        <div class="auth-card" style="max-width: 400px; text-align: center; border: 1px solid var(--primary); position: relative;">
            <button class="badge" onclick="closeAllModals()" style="position: absolute; top: 15px; right: 15px; background: #ef4444; border: none; cursor: pointer; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">&times;</button>
            <h2 style="font-size: 20px; margin-bottom: 5px;" id="qrInfo">Invoice</h2>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">Scan to make payment</p>
            
            <div id="qrCodeContainer" style="margin-bottom: 20px;">
                <!-- QR Image loaded via JS -->
            </div>
            
            <div style="font-size: 24px; font-weight: 800; color: var(--primary); margin-bottom: 20px;" id="qrAmount">₹0.00</div>
        </div>
    </div>

    <!-- Payment Simulator Modal -->
    <div id="simulatorModal" class="modal" style="display: none; align-items: center; justify-content: center; z-index: 2000;">
        <div class="auth-card" style="max-width: 450px; width: 90%; background: #fff; color: #1e293b; padding: 0; overflow: hidden; border-radius: 24px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
            <!-- Header -->
            <div style="background: #1e293b; padding: 25px; text-align: center; color: white;">
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <i class="fas fa-shield-check" style="color: #10b981; font-size: 24px;"></i>
                    <span style="font-weight: 800; letter-spacing: 1px; font-size: 18px;">SECURE GATEWAY</span>
                </div>
                <p style="opacity: 0.7; font-size: 12px; margin: 0;">Transaction ID: <span id="simInvoice">INV-0000</span></p>
            </div>

            <!-- Body -->
            <div style="padding: 30px; background: #f8fafc;">
                <div id="sim-step-1">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <p style="color: #64748b; font-size: 14px; margin-bottom: 5px;">Total Amount to Pay</p>
                        <h1 style="font-size: 42px; margin: 0; color: #0f172a;" id="simAmount">₹0.00</h1>
                    </div>

                    <div style="display: grid; gap: 12px; margin-bottom: 30px;">
                        <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: white; border: 1px solid #e2e8f0; border-radius: 12px;">
                            <i class="fab fa-google-pay" style="font-size: 24px; color: #4285F4;"></i>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; font-size: 14px;">Google Pay / UPI</div>
                                <div style="font-size: 11px; color: #64748b;">Direct bank transfer</div>
                            </div>
                            <input type="radio" name="paymethod" checked>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; opacity: 0.6;">
                            <i class="fas fa-credit-card" style="font-size: 20px; color: #6366f1;"></i>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; font-size: 14px;">Credit / Debit Card</div>
                                <div style="font-size: 11px; color: #64748b;">Visa, Mastercard, RuPay</div>
                            </div>
                        </div>
                    </div>

                    <button class="btn" id="simPayBtn" onclick="processSimPayment(event, sale_id_global)" style="width: 100%; height: 55px; background: #0f172a; color: white; border-radius: 12px; font-weight: 700; font-size: 16px; transition: 0.3s; cursor: pointer;">Pay Now</button>
                    <p style="text-align: center; font-size: 11px; color: #94a3b8; margin-top: 20px;">
                        <i class="fas fa-lock"></i> 256-bit SSL Encrypted Simulation
                    </p>
                </div>

                <!-- Success State -->
                <div id="sim-step-2" style="display: none; text-align: center; padding: 20px 0;">
                    <div style="width: 80px; height: 80px; background: #dcfce7; color: #15803d; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 40px; animation: bounceIn 0.5s;">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2 style="font-size: 24px; color: #0f172a; margin-bottom: 10px;">Payment Successful!</h2>
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 25px;">Your transaction has been authorized by the bank and sent to Warehouse Pro.</p>
                    <div style="padding: 15px; background: #f1f5f9; border-radius: 12px; font-family: monospace; font-size: 12px; color: #475569;">
                        VERIFICATION SIGNAL SENT...
                    </div>
                    <p style="font-size: 11px; color: #94a3b8; margin-top: 25px;">This window will close automatically.</p>
                </div>
            </div>
        </div>
    </div>



    <script>
        const CURRENCY_SYMBOL = '<?php echo CURRENCY_SYMBOL; ?>';
        window.CURRENCY_SYMBOL = CURRENCY_SYMBOL;
        let sale_id_global = null;
        function loadInvoices() {
            fetch('../../actions/get_sales.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('invoiceTableBody');
                    tbody.innerHTML = '';
                    data.forEach(sale => {
                        const statusClass = sale.payment_status.toLowerCase() === 'paid' ? 'badge-success' : 'badge-danger';
                        const displayName = sale.customer_name || sale.walkin_name || 'Walk-in Customer';
                        tbody.innerHTML += `
                            <tr>
                                <td style="font-weight: 600;">${sale.invoice_no}</td>
                                <td>${sale.sale_date}</td>
                                <td>${displayName}</td>
                                <td style="font-weight: 600; color: var(--primary);">${CURRENCY_SYMBOL}${sale.total_amount}</td>
                                <td>
                                    <span class="badge ${statusClass}">${sale.payment_status}</span>
                                    ${sale.transaction_ref ? `<div style="font-size: 10px; color: var(--text-muted); margin-top: 4px;">ID: ${sale.transaction_ref}</div>` : ''}
                                </td>
                                <td style="text-align: right;">
                                    <button class="badge" style="background: var(--warning); border:none; cursor:pointer;" onclick="showQR(${sale.id}, ${sale.total_amount}, '${sale.invoice_no}')"><i class="fas fa-qrcode"></i> Show QR</button>
                                    
                                    ${(sale.payment_status && sale.payment_status.toLowerCase() === 'paid') 
                                        ? `<button class="badge" style="background: #10b981; border:none; opacity: 0.6; cursor: default;"><i class="fas fa-check-double"></i> Paid</button>`
                                        : `<button class="badge" style="background: var(--success); border:none; cursor:pointer; margin-left:8px;" onclick="markAsPaid(${sale.id}, event)"><i class="fas fa-check"></i> Mark Paid</button>`
                                    }

                                    <button class="badge badge-success" style="border:none; cursor:pointer; margin-left:8px;" onclick="printInvoice(${sale.id})">Download PDF</button>
                                     <button class="badge" style="background: var(--primary); border:none; cursor:pointer; margin-left:8px;" onclick="emailInvoice(${sale.id}, '${sale.customer_email || ''}')"><i class="fas fa-envelope"></i> Email</button>

                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <button class="badge" style="background: #ef4444; border:none; cursor:pointer; margin-left:8px;" onclick="deleteInvoice(${sale.id}, '${sale.invoice_no}')"><i class="fas fa-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        function deleteInvoice(id, invNo) {
            if(!confirm(`⚠️ WARNING: Are you sure you want to delete Invoice ${invNo}? This will remove the record and REVERT the stock levels. This action cannot be undone.`)) return;
            
            fetch('../../actions/delete_invoice.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Invoice deleted and stock reverted.');
                    loadInvoices();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function printInvoice(id) {
            fetch('../../actions/get_sale_details.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if(!data.success) return alert('Failed to fetch invoice details.');
                    
                    const { sale, items } = data;
                    let itemsHtml = '';
                    items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${item.product_name}</td>
                                <td>${item.quantity}</td>
                                <td>${CURRENCY_SYMBOL}${item.unit_price}</td>
                                <td>${CURRENCY_SYMBOL}${(item.quantity * item.unit_price).toFixed(2)}</td>
                            </tr>
                        `;
                    });

                    const printWindow = window.open('', '_blank');
                    const companyName = '<?php echo $settings['company_name'] ?? 'Warehouse Pro'; ?>';
                    const companyAddr = '<?php echo $settings['company_address'] ?? ''; ?>';
                    const companyGST = '<?php echo $settings['company_gstin'] ?? ''; ?>';
                    const baseAmount = (sale.total_amount - (parseFloat(sale.cgst_amount) + parseFloat(sale.sgst_amount))).toFixed(2);

                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Invoice - ${sale.invoice_no}</title>
                            <style>
                                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #1e293b; line-height: 1.6; }
                                .invoice-container { max-width: 800px; margin: auto; }
                                .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #6366f1; padding-bottom: 30px; margin-bottom: 30px; }
                                .logo-area h1 { margin: 0; color: #6366f1; font-size: 32px; letter-spacing: -1px; }
                                .logo-area p { margin: 5px 0 0; color: #64748b; font-weight: 500; }
                                .invoice-meta { text-align: right; }
                                .invoice-meta h2 { margin: 0; font-size: 14px; text-transform: uppercase; color: #94a3b8; }
                                .invoice-meta p { margin: 5px 0; font-weight: 700; font-size: 18px; }
                                
                                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
                                .info-box h3 { font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
                                .info-box p { margin: 2px 0; font-size: 14px; }

                                table { width: 100%; border-collapse: collapse; margin: 30px 0; }
                                th { text-align: left; background: #f8fafc; padding: 12px; border-bottom: 2px solid #e2e8f0; font-size: 13px; text-transform: uppercase; color: #64748b; }
                                td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
                                
                                .totals-area { margin-left: auto; width: 300px; border-top: 2px solid #f1f5f9; padding-top: 20px; }
                                .total-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
                                .grand-total { border-top: 2px solid #6366f1; margin-top: 15px; padding-top: 15px; font-size: 22px; font-weight: 800; color: #6366f1; }
                                
                                .footer { margin-top: 80px; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 20px; color: #94a3b8; font-size: 12px; }
                                @media print { .no-print { display: none; } }
                            </style>
                        </head>
                        <body>
                            <div class="invoice-container">
                                <div class="header">
                                    <div class="logo-area">
                                        <h1>${companyName.toUpperCase()}</h1>
                                        <p style="font-size: 11px; margin-top: 5px; color: #64748b;">${companyAddr}</p>
                                        <p style="font-size: 11px; color: #64748b;">GSTIN: ${companyGST}</p>
                                    </div>
                                    <div class="invoice-meta">
                                        <h2>Tax Invoice</h2>
                                        <p>#${sale.invoice_no}</p>
                                        <div style="font-size: 13px; color: #64748b; font-weight: 400;">Date: ${sale.sale_date}</div>
                                    </div>
                                </div>

                                <div class="info-grid">
                                    <div class="info-box">
                                        <h3>Billed To</h3>
                                        <p><strong>${sale.customer_name || sale.walkin_name || 'Walk-in Customer'}</strong></p>
                                        <p>${sale.customer_address || sale.walkin_address || 'No Address Provided'}</p>
                                        <p>Phone: ${sale.customer_phone || sale.walkin_contact || 'N/A'}</p>
                                        <p>GSTIN: ${sale.customer_gstin || 'Unregistered'}</p>
                                    </div>
                                    <div class="info-box" style="text-align: right;">
                                        <h3>Payment Details</h3>
                                        <p>Method: <span style="font-weight:700;">${sale.payment_method || 'N/A'}</span></p>
                                        <p>Status: <span style="font-weight:700; color: ${sale.payment_status === 'Paid' ? '#10b981' : '#f43f5e'};">${sale.payment_status}</span></p>
                                        ${sale.payment_status !== 'Paid' ? `
                                            <div style="margin-top: 10px;">
                                                <p style="font-size: 10px; margin-bottom: 5px; color: var(--text-muted);">Scan to pay via GPay / Paytm</p>
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=upi://pay?pa=${encodeURIComponent('<?php echo $settings['merchant_upi'] ?? 'merchant@upi'; ?>')}%26pn=${encodeURIComponent(companyName)}%26am=${sale.total_amount}%26tn=INV-${id}" style="border: 1px solid #ddd; padding: 5px; border-radius: 8px;">
                                            </div>
                                        ` : `
                                            <p style="font-size: 12px; color: #10b981;"><i class="fas fa-check-circle"></i> Transaction Confirmed</p>
                                            <p style="font-size: 11px; color: var(--text-muted);">Ref: ${sale.transaction_ref || 'N/A'}</p>
                                        `}
                                    </div>
                                </div>

                                <table>
                                    <thead>
                                        <tr>
                                            <th>Item Description</th>
                                            <th>HSN</th>
                                            <th style="text-align: center;">Qty</th>
                                            <th style="text-align: right;">Rate</th>
                                            <th style="text-align: right;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${items.map(item => `
                                            <tr>
                                                <td>${item.product_name}</td>
                                                <td style="color: #94a3b8;">${item.hsn_code || 'N/A'}</td>
                                                <td style="text-align: center;">${item.quantity}</td>
                                                <td style="text-align: right;">${CURRENCY_SYMBOL}${item.unit_price}</td>
                                                <td style="text-align: right;">${CURRENCY_SYMBOL}${(item.quantity * item.unit_price).toFixed(2)}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>

                                <div class="totals-area">
                                    <div class="total-row"><span>Taxable Value:</span><span>${CURRENCY_SYMBOL}${baseAmount}</span></div>
                                    <div class="total-row"><span>CGST:</span><span>${CURRENCY_SYMBOL}${parseFloat(sale.cgst_amount).toFixed(2)}</span></div>
                                    <div class="total-row"><span>SGST:</span><span>${CURRENCY_SYMBOL}${parseFloat(sale.sgst_amount).toFixed(2)}</span></div>
                                    <div class="total-row grand-total"><span>Total:</span><span>${CURRENCY_SYMBOL}${sale.total_amount}</span></div>
                                </div>

                                <div class="footer">
                                    <p>Registered Office: ${companyAddr}</p>
                                    <p>This is a GST-compliant digital document. No signature required.</p>
                                </div>
                                <div class="no-print" style="margin-top: 40px; text-align: center;">
                                    <button onclick="window.print()" style="padding: 12px 40px; background: #6366f1; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Confirm & Print Invoice</button>
                                </div>
                            </div>
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                });
        }
        function emailInvoice(id, email) {
            if(!email) {
                email = prompt("Please enter customer's email address:", "");
                if(!email) return;
            }
            if(!confirm("Send invoice INV-" + String(id).padStart(5, '0') + " to " + email + "?")) return;
            
            fetch('../../actions/send_invoice_email.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id, email })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) alert("Invoice sent successfully!");
                else alert("Failed to send: " + data.message);
            });
        }
        let statusPoller = null;

        function showQR(id, amount, sale_no) {
            sale_id_global = id;
            const modal = document.getElementById('paymentModal');
            const merchantUpi = '<?php echo $settings['merchant_upi'] ?? 'merchant@upi'; ?>';
            const companyName = '<?php echo $settings['company_name'] ?? 'WarehousePro'; ?>';
            
            document.getElementById('qrInfo').innerText = "Invoice #" + sale_no;
            document.getElementById('qrAmount').innerText = window.CURRENCY_SYMBOL + parseFloat(amount).toFixed(2);
            
            const upiUrl = `upi://pay?pa=${encodeURIComponent(merchantUpi)}&pn=${encodeURIComponent(companyName)}&am=${amount}&tn=${id}`;
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(upiUrl)}`;
            
            document.getElementById('qrCodeContainer').innerHTML = `
                <div style="background: white; padding: 20px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <img src="${qrUrl}" style="max-width: 100%; height: auto; border: none; margin-bottom: 15px;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 5px;">
                        <img src="https://www.gstatic.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png" height="20" style="opacity: 0.8;">
                        <span style="font-weight: 700; color: #5f6368; font-size: 14px;">Pay</span>
                    </div>
                </div>
                
                <p style="font-size: 11px; color: var(--primary); margin-bottom: 20px;"><i class="fas fa-sync fa-spin"></i> Waiting for payment confirmation...</p>
                
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    <a href="${upiUrl}" class="btn" style="background: #1a73e8; color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 14px; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);">
                        <i class="fab fa-google-pay" style="font-size: 24px;"></i> Open in App (Mobile Only)
                    </a>
                    
                    <button class="btn" style="background: var(--bg-dark); color: var(--text-main); border: 1px solid var(--border-color); font-size: 12px; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 8px;" onclick="copyUPI('${merchantUpi}')">
                        <i class="fas fa-copy"></i> Copy UPI ID: ${merchantUpi}
                    </button>
                </div>

                <div style="margin: 20px 0; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 15px;">- AUTOMATED SIMULATION -</p>
                    <button class="btn" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none; font-weight: 700; padding: 15px; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);" onclick="payViaSimulator(${id}, ${amount}, '${sale_no}')">
                        <i class="fas fa-bolt"></i> Local Payment Simulation
                    </button>
                </div>

                <button class="btn" onclick="markAsPaid(${id}, event)" style="margin-top: 0; font-size: 12px; padding: 10px; background: transparent; color: var(--text-muted); border: 1px dashed var(--border-color);">Manual Confirmation</button>
            `;
            modal.style.display = 'flex';

            // Polling for "Auto-Show" Success
            if (statusPoller) clearInterval(statusPoller);
            statusPoller = setInterval(() => {
                fetch('../../actions/get_sale_details.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.sale.payment_status.toLowerCase() === 'paid') {
                        clearInterval(statusPoller);
                        // Hide both modals
                        document.getElementById('paymentModal').style.display = 'none';
                        document.getElementById('simulatorModal').style.display = 'none';
                        
                        alert("✅ AUTOMATED SUCCESS!\nInvoice " + sale_no + " updated via Simulator/Webhook.");
                        loadInvoices();
                    }
                });
            }, 2500); 
        }



        function copyUPI(upi) {
            navigator.clipboard.writeText(upi).then(() => {
                alert('UPI ID copied: ' + upi);
            });
        }

        function payViaSimulator(id, amount, sale_no) {
            const sim = document.getElementById('simulatorModal');
            document.getElementById('simInvoice').innerText = sale_no;
            document.getElementById('simAmount').innerText = (window.CURRENCY_SYMBOL || '₹') + amount;
            sim.style.display = 'flex';
            
            // Step logic
            document.getElementById('sim-step-1').style.display = 'block';
            document.getElementById('sim-step-2').style.display = 'none';
        }

        function closeAllModals() {
            if (statusPoller) clearInterval(statusPoller);
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('simulatorModal').style.display = 'none';
        }

        function processSimPayment(e, id) {
            const btn = e.target || document.getElementById('simPayBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Communicating with Bank...';
            btn.disabled = true;

            if (!id) {
                alert("Error: Missing Invoice ID. Please reopen the QR modal.");
                btn.disabled = false;
                btn.innerHTML = 'Pay Now';
                return;
            }

            fetch('../../actions/payment_webhook.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    invoice_id: id,
                    status: 'success',
                    transaction_id: 'SIM-' + Math.random().toString(36).substr(2, 9).toUpperCase()
                })
            })
            .then(res => {
                if(!res.ok) throw new Error("Server returned error " + res.status);
                return res.json();
            })
            .then(data => {
                // Success state UI
                setTimeout(() => {
                    document.getElementById('sim-step-1').style.display = 'none';
                    document.getElementById('sim-step-2').style.display = 'block';
                }, 1500);
            })
            .catch(err => {
                alert("⚠️ SIMULATOR ERROR: " + err.message);
                btn.disabled = false;
                btn.innerHTML = 'Pay Now';
            });
        }

        function markAsPaid(id, e) {
            // Safer event detection (handles both passed event and global window.event)
            const event = e || window.event;
            
            const ref = prompt("Please enter Transaction Reference or payment details (e.g. GPay ID, Cash, Check No.).\n\nYou can leave this empty for 'MANUAL' marking.", "");
            
            if(ref === null) return;

            const targetBtn = event ? (event.target.closest('button') || event.target) : null;
            const originalContent = targetBtn ? targetBtn.innerHTML : null;
            
            if (targetBtn) {
                targetBtn.disabled = true;
                targetBtn.style.opacity = '0.7';
                targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }

            fetch('../../actions/update_payment_status.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id, ref: ref.trim() || 'MANUAL' })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    closeAllModals(); // Close any open payment/simulator modals
                    
                    setTimeout(() => {
                        alert("✅ Payment Confirmed and Recorded!");
                        loadInvoices();
                    }, 100);
                } else {
                    alert("Error: " + data.message);
                    if (targetBtn) {
                        targetBtn.disabled = false;
                        targetBtn.style.opacity = '1';
                        targetBtn.innerHTML = originalContent;
                    }
                }
            })
            .catch(err => {
                alert("Network failure. Please check your connection.");
                if (targetBtn) {
                    targetBtn.disabled = false;
                    targetBtn.style.opacity = '1';
                    targetBtn.innerHTML = originalContent;
                }
            });
        }
        
        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            loadInvoices();

            // Check for Auto-Payment trigger (from POS)
            const urlParams = new URLSearchParams(window.location.search);
            const autoPayId = urlParams.get('pay_now');
            if (autoPayId) {
                setTimeout(() => {
                    // Try to find the sale in the UI (or just fetch its details directly)
                    fetch('../../actions/get_sale_details.php?id=' + autoPayId)
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            showQR(d.sale.id, d.sale.total_amount, d.sale.invoice_no);
                            // Clean URL
                            window.history.replaceState({}, document.title, window.location.pathname);
                        }
                    });
                }, 800);
            }
        });
    </script>
</body>
</html>
