<?php
/**
 * Warehouse Pro - Universal Top Navigation
 */
$base_url = '/warehouse/';
?>
<header class="top-nav" style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; height: 70px; background: var(--card-bg); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; padding: 0 40px; z-index: 90;">
    <!-- Real-Time Profit Tracker -->
    <div class="profit-ticker" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); padding: 8px 16px; border-radius: 12px; display: flex; align-items: center; gap: 10px; margin-right: 20px;">
        <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981; animation: pulse 2s infinite;"></div>
        <span style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Live Profit</span>
        <span id="liveProfitDisplay" style="font-weight: 800; color: #10b981; font-family: monospace; font-size: 16px;"><?php echo CURRENCY_SYMBOL; ?>0.00</span>
    </div>

    <div class="nav-search-wrapper" style="position: relative; flex: 1; max-width: 400px;">
        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        <input type="text" id="globalSearch" placeholder="Search Invoices, Products, Customers..." 
            style="width: 100%; padding: 10px 15px 10px 45px; background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-main);">
        <div id="globalResults" style="position: absolute; top: 110%; left: 0; right: 0; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; display: none; max-height: 400px; overflow-y: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.3); z-index: 100;"></div>
    </div>

    <div class="top-nav-right" style="display: flex; align-items: center; gap: 24px;">
        <!-- Notification Bell -->
        <div style="position: relative; cursor: pointer;" id="notifWrapper">
            <i class="fas fa-bell" style="color: var(--text-main); font-size: 20px;"></i>
            <span id="notifBadge" style="position: absolute; top: -5px; right: -5px; background: var(--danger); color: white; border-radius: 50%; width: 15px; height: 15px; font-size: 10px; display: none; align-items: center; justify-content: center; font-weight: 700;">0</span>
            <div id="notifDropdown" style="position: absolute; top: 130%; right: 0; width: 300px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; display: none; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.3); z-index: 100;">
                <div style="padding: 15px; border-bottom: 1px solid var(--border-color); font-weight: 700;">Notifications</div>
                <div id="notifList" style="max-height: 300px; overflow-y: auto;"></div>
            </div>
        </div>

        <button id="themeToggle" style="background: none; border: none; color: var(--text-main); cursor: pointer; font-size: 20px;">
            <i class="fas fa-moon"></i>
        </button>

        <div style="display: flex; align-items: center; gap: 8px; background: rgba(99,102,241,0.1); padding: 5px 12px; border-radius: 20px; border: 1px solid rgba(99,102,241,0.2);">
            <i class="fas fa-star" style="color: var(--primary); font-size: 12px;"></i>
            <span id="userXP" style="font-size: 13px; font-weight: 700; color: var(--primary);"><?php 
                $uid = $_SESSION['user_id'];
                $xp_res = $conn->query("SELECT xp FROM users WHERE id = $uid");
                echo number_format($xp_res->fetch_row()[0] ?? 0);
            ?> XP</span>
        </div>

        <a href="<?php echo $base_url; ?>modules/admin/profile.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text-main);">
            <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user" style="color: white; font-size: 14px;"></i>
            </div>
            <span style="font-weight: 500; font-size: 14px;"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
        </a>
    </div>
</header>

<script>
    // Audio Unlock System for Notifications
    let audioUnlocked = false;
    const notifAudio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

    function unlockAudio() {
        if (audioUnlocked) return;
        notifAudio.play().then(() => {
            notifAudio.pause();
            notifAudio.currentTime = 0;
            audioUnlocked = true;
            console.log("Audio Notifications Unlocked");
            document.removeEventListener('click', unlockAudio);
            document.removeEventListener('keydown', unlockAudio);
        }).catch(e => console.error("Unlock failed", e));
    }

    document.addEventListener('click', unlockAudio);
    document.addEventListener('keydown', unlockAudio);
    // System Symbols
    if (typeof window.CURRENCY_SYMBOL === 'undefined') {
        window.CURRENCY_SYMBOL = '<?php echo CURRENCY_SYMBOL; ?>';
    }

    // Theme UI Logic
    const themeBtn = document.getElementById('themeToggle');
    const themeIcon = themeBtn.querySelector('i');
    
    function applyThemeUI() {
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            document.documentElement.classList.remove('light-mode');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }
    }
    applyThemeUI();

    themeBtn.addEventListener('click', () => {
        if (document.documentElement.classList.contains('light-mode')) {
            document.documentElement.classList.remove('light-mode');
            localStorage.setItem('theme', 'dark');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        } else {
            document.documentElement.classList.add('light-mode');
            localStorage.setItem('theme', 'light');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        }
    });

    // Global Search Ajax
    const globalSearchInput = document.getElementById('globalSearch');
    const globalResultsBox = document.getElementById('globalResults');

    globalSearchInput.addEventListener('input', (e) => {
        const query = e.target.value;
        if (query.length < 2) {
            globalResultsBox.style.display = 'none';
            return;
        }

        fetch('<?php echo $base_url; ?>actions/search_global.php?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                globalResultsBox.innerHTML = '';
                globalResultsBox.style.display = 'block';
                
                if(data.products.length === 0 && data.invoices.length === 0 && data.customers.length === 0) {
                    globalResultsBox.innerHTML = '<div style="padding:15px; color:var(--text-muted);">No results found...</div>';
                    return;
                }
                
                // Show Products
                if(data.products.length > 0) {
                    globalResultsBox.innerHTML += '<div style="padding:5px 15px; background:var(--glass-bg); font-size:11px; font-weight:700; color:var(--primary);">PRODUCTS</div>';
                    data.products.forEach(p => {
                        globalResultsBox.innerHTML += `
                            <a href="<?php echo $base_url; ?>modules/product/inventory.php" style="display:block; padding:12px; text-decoration:none; border-bottom:1px solid var(--border-color);">
                                <div style="font-weight:600; color:var(--text-main); font-size:13px;">${p.name}</div>
                                <div style="font-size:11px; color:var(--text-muted);">SKU: ${p.sku || 'N/A'} | Price: ${CURRENCY_SYMBOL}${p.price}</div>
                            </a>
                        `;
                    });
                }
                // Show Invoices
                if(data.invoices.length > 0) {
                    globalResultsBox.innerHTML += '<div style="padding:5px 15px; background:var(--glass-bg); font-size:11px; font-weight:700; color:var(--primary);">INVOICES</div>';
                    data.invoices.forEach(i => {
                        globalResultsBox.innerHTML += `
                            <a href="<?php echo $base_url; ?>modules/sell/invoices.php" style="display:block; padding:12px; text-decoration:none; border-bottom:1px solid var(--border-color);">
                                <div style="font-weight:600; color:var(--text-main); font-size:13px;">Invoice #INV-${String(i.id).padStart(5, '0')}</div>
                                <div style="font-size:11px; color:var(--text-muted);">Amount: ${CURRENCY_SYMBOL}${i.total_amount} | Date: ${i.sale_date}</div>
                            </a>
                        `;
                    });
                }
            });
    });

    // Close search on click outside
    document.addEventListener('click', (e) => {
        if(!globalSearchInput.contains(e.target) && !globalResultsBox.contains(e.target)) {
            globalResultsBox.style.display = 'none';
        }
    });

    // Dropdown Toggles
    const wrapper = document.getElementById('notifWrapper');
    const dropdown = document.getElementById('notifDropdown');
    wrapper.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
    });
    document.addEventListener('click', () => { dropdown.style.display = 'none'; });

    // Notification Sound & Sync
    let lastNotifCount = 0;

    function checkNotifications() {
        const badge = document.getElementById('notifBadge');
        const notifList = document.getElementById('notifList');
        
        fetch('<?php echo $base_url; ?>actions/get_notifications.php')
            .then(res => res.json())
            .then(data => {
                const count = data.length || 0;
                
                // Play sound if new alerts appeared and audio is unlocked
                if(count > lastNotifCount && audioUnlocked) {
                    notifAudio.play().catch(e => console.log("Sound failed", e));
                }
                lastNotifCount = count;

                if(count > 0) {
                    badge.style.display = 'flex';
                    badge.innerHTML = count;
                    
                    notifList.innerHTML = '';
                    data.forEach(n => {
                        notifList.innerHTML += `
                            <a href="${n.link}" style="display:block; padding:12px; border-bottom:1px solid var(--border-color); text-decoration:none; transition:0.3s;">
                                <div style="font-weight:700; color:var(--danger); font-size:12px;">${n.title}</div>
                                <div style="font-size:11px; color:var(--text-main); margin-top:2px;">${n.message}</div>
                            </a>
                        `;
                    });
                } else {
                    badge.style.display = 'none';
                    notifList.innerHTML = '<div style="padding:20px; text-align:center; color:var(--text-muted); font-size:11px;">No pending alerts.</div>';
                }
            });
    }
    checkNotifications();
    setInterval(checkNotifications, 30000); // Check every 30 seconds
</script>
<style>
    .top-nav input:focus { outline: none; border-color: var(--primary) !important; }
    .search-result-item:hover { background: rgba(255, 255, 255, 0.05); }
    .main-content { margin-top: 70px !important; }
</style>

<script>
    function updateLiveProfit() {
        fetch('<?php echo $base_url; ?>actions/get_live_profit.php')
            .then(res => res.json())
            .then(data => {
                const display = document.getElementById('liveProfitDisplay');
                if (display) {
                    const amount = parseFloat(data.profit).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    display.innerText = '<?php echo CURRENCY_SYMBOL; ?>' + amount;
                }
            });
    }
    
    // Initial call and set interval
    updateLiveProfit();
    setInterval(updateLiveProfit, 5000);
</script>
