<?php
/**
 * Warehouse Pro - Operational Dashboard
 * Role: Central hub for stats, analytics, and navigation
 */
$page_title = 'Dashboard';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch aggregate statistics for overview cards
$stats = [
    'products' => 0,
    'customers' => 0,
    'total_sales' => 0,
    'total_purchases' => 0
];

$res1 = $conn->query("SELECT COUNT(*) FROM products");
if ($res1) $stats['products'] = $res1->fetch_row()[0];

$res2 = $conn->query("SELECT COUNT(*) FROM customers");
if ($res2) $stats['customers'] = $res2->fetch_row()[0];

$res_cat = $conn->query("SELECT COUNT(*) FROM categories");
$stats['categories'] = ($res_cat) ? $res_cat->fetch_row()[0] : 0;

$res_ven = $conn->query("SELECT COUNT(*) FROM vendors");
$stats['vendors'] = ($res_ven) ? $res_ven->fetch_row()[0] : 0;

$res3 = $conn->query("SELECT SUM(total_amount) FROM sales");
if ($res3) $stats['total_sales'] = $res3->fetch_row()[0] ?? 0;

$res4 = $conn->query("SELECT SUM(total_amount) FROM purchases");
if ($res4) $stats['total_purchases'] = $res4->fetch_row()[0] ?? 0;

// Estimated Gross Profit Calculation (Sale Price - Buy Price)
$stats['total_profit'] = 0;
$res5 = $conn->query("SELECT SUM(si.quantity * (si.unit_price - p.purchase_price)) 
                      FROM sale_items si 
                      JOIN products p ON si.product_id = p.id");
if ($res5) $stats['total_profit'] = $res5->fetch_row()[0] ?? 0;

// XP Based Humanized Ranks
function getUserRank($xp) {
    if ($xp < 100) return "Novice Handler";
    if ($xp < 500) return "Logistics Specialist";
    if ($xp < 1000) return "Inventory Strategist";
    if ($xp < 2500) return "Distribution Architect";
    return "Master Logistician";
}

$user_id = $_SESSION['user_id'];
$xp_res = $conn->query("SELECT xp FROM users WHERE id = $user_id");
$current_xp = $xp_res->fetch_row()[0] ?? 0;
$user_rank = getUserRank($current_xp);

// Time-based greeting
$hour = date('H');
$greeting = "Good Day";
if ($hour < 12) $greeting = "Good Morning";
elseif ($hour < 17) $greeting = "Good Afternoon";
else $greeting = "Good Evening";
?>

<main class="main-content">
    <header class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 28px; font-weight: 800; letter-spacing: -0.5px;"><?php echo $greeting; ?>, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
                <i class="fas fa-medal" style="color: var(--warning);"></i> 
                Ranked: <strong style="color: var(--text-main);"><?php echo $user_rank; ?></strong> 
                <span style="opacity: 0.5; margin: 0 8px;">|</span>
                Operational status: <span style="color: var(--success); font-weight: 600;">Optimal</span>
            </p>
        </div>
        <div id="calendarTrigger" style="background: var(--card-bg); padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border-color); cursor: pointer; display: flex; align-items: center; gap: 10px; transition: 0.3s; height: 45px;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <i class="far fa-calendar-alt" style="color: var(--primary);"></i> 
            <span id="currentDateDisplay"><?php echo date('M d, Y'); ?></span>
            <i class="fas fa-chevron-down" style="font-size: 10px; opacity: 0.5;"></i>
        </div>
    </header>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Stats Cards -->
        <div class="stat-card" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div style="width: 50px; height: 50px; background: rgba(99, 102, 241, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                <i class="fas fa-boxes" style="font-size: 20px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase;">Total SKUs</p>
                <h2 style="font-size: 28px;"><?php echo number_format($stats['products']); ?></h2>
            </div>
        </div>

        <?php if ($role == 'admin' || $role == 'product_dept') : ?>
        <div class="stat-card" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
            <p style="color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase;">Total Categories</p>
            <h2 style="font-size: 28px; color: var(--primary); margin-top: 8px;"><?php echo number_format($stats['categories']); ?></h2>
        </div>
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'sell_dept') : ?>
        <div class="stat-card" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--success);">
                <i class="fas fa-chart-line" style="font-size: 20px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase;">Revenue</p>
                <h2 style="font-size: 28px; color: var(--success);"><?php echo formatMoney($stats['total_sales']); ?></h2>
            </div>
        </div>
        <div class="stat-card" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--warning);">
                <i class="fas fa-users" style="font-size: 20px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase;">Total Customers</p>
                <h2 style="font-size: 28px; color: var(--text-main);"><?php echo number_format($stats['customers']); ?></h2>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($role == 'admin') : ?>
        <div class="stat-card" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div style="width: 50px; height: 50px; background: rgba(139, 92, 246, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                <i class="fas fa-hand-holding-usd" style="font-size: 20px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase;">Net Profit (Est)</p>
                <h2 style="font-size: 28px; color: #8b5cf6;"><?php echo formatMoney($stats['total_profit']); ?></h2>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($role == 'admin' || $role == 'purchase_dept') : ?>
        <div class="stat-card" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
            <p style="color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase;">Procurement</p>
            <h2 style="font-size: 28px; color: var(--danger); margin-top: 8px;"><?php echo formatMoney($stats['total_purchases']); ?></h2>
        </div>

        <div class="stat-card" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                <i class="fas fa-truck" style="font-size: 20px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase;">Total Vendors</p>
                <h2 style="font-size: 28px; color: var(--text-main);"><?php echo number_format($stats['vendors']); ?></h2>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($role == 'inventory_dept') : 
            $low_stock = $conn->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= 10")->fetch_row()[0];
        ?>
        <div class="stat-card" style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
            <p style="color: var(--text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase;">Low Stock Items</p>
            <h2 style="font-size: 28px; color: var(--danger); margin-top: 8px;"><?php echo $low_stock; ?></h2>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick Access Actions -->
    <div style="margin-bottom: 32px;">
        <h3 style="margin-bottom: 16px; font-size: 18px;">Quick Actions</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <?php if ($role == 'admin' || $role == 'sell_dept') : ?>
            <a href="modules/sell/sales.php" style="display: flex; align-items: center; gap: 16px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); padding: 20px; border-radius: 16px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(99, 102, 241, 0.2)'" onmouseout="this.style.background='rgba(99, 102, 241, 0.1)'">
                <i class="fas fa-shopping-cart" style="font-size: 24px; color: #6366f1;"></i>
                <div>
                    <div style="font-weight: 700; color: var(--text-main);">New Sale</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Process customers</div>
                </div>
            </a>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'product_dept') : ?>
            <a href="modules/product/inventory.php" style="display: flex; align-items: center; gap: 16px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); padding: 20px; border-radius: 16px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'">
                <i class="fas fa-box-open" style="font-size: 24px; color: #10b981;"></i>
                <div>
                    <div style="font-weight: 700; color: var(--text-main);">Inventory</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Stock management</div>
                </div>
            </a>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'inventory_dept') : ?>
            <a href="modules/inventory/stock.php" style="display: flex; align-items: center; gap: 16px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); padding: 20px; border-radius: 16px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'">
                <i class="fas fa-warehouse" style="font-size: 24px; color: #10b981;"></i>
                <div>
                    <div style="font-weight: 700; color: var(--text-main);">Stock Status</div>
                    <div style="font-size: 12px; color: var(--text-muted);">View availability</div>
                </div>
            </a>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'sell_dept') : ?>
            <a href="modules/sell/invoices.php" style="display: flex; align-items: center; gap: 16px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); padding: 20px; border-radius: 16px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(245, 158, 11, 0.2)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.1)'">
                <i class="fas fa-file-invoice" style="font-size: 24px; color: #f59e0b;"></i>
                <div>
                    <div style="font-weight: 700; color: var(--text-main);">Billing</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Review invoices</div>
                </div>
            </a>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'purchase_dept') : ?>
            <a href="modules/purchase/orders.php" style="display: flex; align-items: center; gap: 16px; background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); padding: 20px; border-radius: 16px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(244, 63, 94, 0.2)'" onmouseout="this.style.background='rgba(244, 63, 94, 0.1)'">
                <i class="fas fa-truck-loading" style="font-size: 24px; color: #f43f5e;"></i>
                <div>
                    <div style="font-weight: 700; color: var(--text-main);">Purchases</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Order procurement</div>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Live Warehouse Stock Chart -->
        <div style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 24px; border-radius: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; color: var(--text-main); margin: 0;">
                    <i class="fas fa-box-open" style="color: #f59e0b;"></i> Main Categories
                </h3>
                <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 10px;">LIVE STOCK</span>
            </div>
            <div style="display: flex; justify-content: center; align-items: center; min-height: 280px; position: relative;">
                <canvas id="stockCategoryChart" style="max-height: 280px;"></canvas>
            </div>
            <div id="categoryLegend" style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-top: 20px;">
                <!-- Legend will be populated by JavaScript -->
            </div>
        </div>

        <!-- AI Demand Forecast -->
        <div style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(168, 85, 247, 0.05)); border: 1px solid rgba(99, 102, 241, 0.15); padding: 24px; border-radius: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <!-- Re-labeled for a more natural feel -->
                <h3 style="font-size: 18px; color: var(--primary); margin: 0;"><i class="fas fa-chart-line"></i> Stock Demand Trends</h3>
                <span class="badge" style="background: rgba(99, 102, 241, 0.1); color: var(--primary); font-size: 10px;">PROJECTIONS</span>
            </div>
            <div id="aiDemandGrid" style="display: flex; flex-direction: column; gap: 12px;">
                <p style="color: var(--text-muted); font-size: 13px;">Analyzing sales patterns...</p>
            </div>
        </div>

        <!-- Leaderboard -->
        <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(99, 102, 241, 0.05)); border: 1px solid rgba(16, 185, 129, 0.15); padding: 24px; border-radius: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; color: #10b981; margin: 0;"><i class="fas fa-trophy"></i> Sales Leaderboard</h3>
                <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 10px;">EXPERIENCE POINTS</span>
            </div>
            <div id="leaderboardGrid" style="display: flex; flex-direction: column; gap: 12px;">
                <p style="color: var(--text-muted); font-size: 13px;">Calculating rankings...</p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: <?php echo ($role == 'admin' || $role == 'sell_dept') ? '2fr 1fr' : '1fr'; ?>; gap: 24px;">
        <?php if ($role == 'admin' || $role == 'sell_dept') : ?>
        <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; flex-direction: column;">
            <h3 style="margin-bottom: 20px; font-size: 18px;">Sales Performance</h3>
            <div style="flex: 1; min-height: 300px; position: relative;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <div style="background: var(--card-bg); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color);">
            <h3 style="margin-bottom: 20px; font-size: 18px;">Live Activity</h3>
            <div id="activityFeed" style="display: flex; flex-direction: column; gap: 16px;">
                <p style="color: var(--text-muted); font-size: 13px;">Syncing activity feed...</p>
            </div>
            
            <!-- Human Touch: Logistics Tip -->
            <div style="margin-top: 30px; padding: 20px; background: rgba(99, 102, 241, 0.05); border-radius: 16px; border-left: 4px solid var(--primary);">
                <h4 style="font-size: 12px; color: var(--primary); text-transform: uppercase; margin-bottom: 8px;"><i class="fas fa-lightbulb"></i> Logistics Tip of the Day</h4>
                <p id="logisticsTip" style="font-size: 13px; color: var(--text-muted); line-height: 1.4;">
                    Efficient bin locations can reduce picking time by up to 30%. Always keep high-velocity items near the dispatch area.
                </p>
            </div>
        </div>
    </div>

    <!-- Onboarding Welcome Modal -->
    <div id="welcomeModal" class="modal" style="display: none; align-items: center; justify-content: center;">
        <div class="auth-card" style="max-width: 500px; text-align: center; border: 2px solid var(--primary);">
            <div style="width: 80px; height: 80px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-rocket" style="font-size: 40px; color: var(--primary);"></i>
            </div>
            <h2 style="font-size: 24px; margin-bottom: 10px;">Welcome Back!</h2>
            <!-- Keeping it simple and direct -->
            <p style="color: var(--text-muted); margin-bottom: 24px; font-size: 14px;">Your stock management portal is ready. Here's a quick look at your tools:</p>
            
            <div style="text-align: left; margin-bottom: 24px; display: grid; gap: 16px;">
                <div style="display: flex; gap: 12px; align-items: center;">
                    <i class="fas fa-search" style="color: var(--primary); width: 20px;"></i>
                    <span style="font-size: 13px;"><strong>Global Search</strong>: Locate any SKU or Invoice at the top.</span>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <i class="fas fa-bell" style="color: var(--danger); width: 20px;"></i>
                    <span style="font-size: 13px;"><strong>Auto-Alerts</strong>: Bell will ring when stock hits low limits.</span>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <i class="fas fa-barcode" style="color: var(--success); width: 20px;"></i>
                    <span style="font-size: 13px;"><strong>Label Printer</strong>: Generate physical labels in Inventory.</span>
                </div>
            </div>

            <button class="btn" onclick="closeWelcome()" style="width: 100%;">Get Started</button>
        </div>
    </div>
</main>

<script>
    function loadAIDemand() {
        const grid = document.getElementById('aiDemandGrid');
        if (!grid) return;

        fetch('actions/get_demand_forecast.php')
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    grid.innerHTML = '<div style="padding: 10px; color: var(--success); font-size: 13px;"><i class="fas fa-check-circle"></i> Stock levels are stable. No immediate runouts predicted.</div>';
                    return;
                }
                grid.innerHTML = '';
                data.forEach(item => {
                    const color = item.status === 'CRITICAL' ? '#f43f5e' : '#f59e0b';
                    grid.innerHTML += `
                        <div style="background: var(--card-bg); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <div style="width: 4px; height: 24px; background: ${color}; border-radius: 4px;"></div>
                                <div>
                                    <div style="font-weight: 700; font-size: 13px; color: var(--text-main);">${item.name}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">Current: ${item.stock} units</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 10px; color: ${color}; font-weight: 800; text-transform: uppercase;">Est. Runout</div>
                                <div style="font-weight: 800; font-size: 16px; color: ${color};">${item.days_left} Days</div>
                            </div>
                        </div>
                    `;
                });
            });
    }

    function loadLeaderboard() {
        const grid = document.getElementById('leaderboardGrid');
        if (!grid) return;

        fetch('actions/get_leaderboard.php')
            .then(res => res.json())
            .then(data => {
                grid.innerHTML = '';
                data.forEach((user, index) => {
                    const medals = ['🥇', '🥈', '🥉'];
                    const award = medals[index] || (index + 1);
                    grid.innerHTML += `
                        <div style="background: var(--card-bg); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <div style="font-size: 18px; width: 30px;">${award}</div>
                                <div>
                                    <div style="font-weight: 700; font-size: 14px; color: var(--text-main);">${user.username}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); text-transform: capitalize;">${user.role.replace('_', ' ')}</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Experience</div>
                                <div style="font-weight: 800; font-size: 14px; color: #10b981;">${user.xp} XP</div>
                            </div>
                        </div>
                    `;
                });
            });
    }

    // Load Live Warehouse Stock Chart
    function loadStockChart() {
        const canvas = document.getElementById('stockCategoryChart');
        if (!canvas) return;

        fetch('actions/get_stock_by_category.php')
            .then(res => res.json())
            .then(data => {
                if (data.error || data.length === 0) {
                    canvas.parentElement.innerHTML = '<p style="color: var(--text-muted); font-size: 13px; text-align: center;">No stock data available</p>';
                    return;
                }

                const labels = data.map(cat => cat.name);
                const stockData = data.map(cat => cat.stock);
                const colors = data.map(cat => cat.color);

                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: stockData,
                            backgroundColor: colors,
                            borderColor: 'rgba(0, 0, 0, 0.1)',
                            borderWidth: 2,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} units (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });

                // Build custom legend
                const legendContainer = document.getElementById('categoryLegend');
                legendContainer.innerHTML = '';
                data.forEach((cat, index) => {
                    const total = stockData.reduce((a, b) => a + b, 0);
                    const percentage = ((cat.stock / total) * 100).toFixed(1);
                    legendContainer.innerHTML += `
                        <div style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; background: var(--glass-bg); border-radius: 8px; border: 1px solid var(--border-color);">
                            <div style="width: 12px; height: 12px; border-radius: 3px; background: ${cat.color};"></div>
                            <span style="font-size: 12px; color: var(--text-main); font-weight: 600;">${cat.name}</span>
                        </div>
                    `;
                });
            })
            .catch(err => {
                console.error('Stock Chart Error:', err);
                canvas.parentElement.innerHTML = '<p style="color: var(--danger); font-size: 13px; text-align: center;">Failed to load stock data</p>';
            });
    }

    // Initialize Dashboard Components
    document.addEventListener('DOMContentLoaded', function() {
        // Just making sure everything loads in the right order
        loadActivities();
        loadAIDemand();
        loadLeaderboard();
        loadStockChart();
        initChart();
        initTips();

        // Initialize Calendar
        flatpickr("#calendarTrigger", {
            defaultDate: "today",
            dateFormat: "Y-m-d",
            theme: "dark",
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('currentDateDisplay').innerText = instance.formatDate(selectedDates[0], "M j, Y");
                // Here you could add logic to filter dashboard stats by date if needed
            }
        });

        // Toggle on-boarding for new sessions
        if (!localStorage.getItem('onboarded')) {
            document.getElementById('welcomeModal').style.display = 'flex';
        }
    });

    function closeWelcome() {
        document.getElementById('welcomeModal').style.display = 'none';
        localStorage.setItem('onboarded', 'true');
    }

    // Refresh live events via API
    function loadActivities() {
        fetch('actions/get_recent_activities.php')
            .then(res => res.json())
            .then(data => {
                const feed = document.getElementById('activityFeed');
                if (data.length === 0) {
                    feed.innerHTML = '<p style="color: var(--text-muted); font-size: 13px;">No recent audit trail logs.</p>';
                    return;
                }
                feed.innerHTML = '';
                data.forEach(act => {
                    feed.innerHTML += `
                        <div style="display: flex; align-items: flex-start; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: ${act.color}20; color: ${act.color}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas ${act.icon}"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h4 style="font-size: 14px; margin: 0;">${act.title}</h4>
                                    <span style="font-size: 12px; color: var(--text-muted);">${act.time}</span>
                                </div>
                                <p style="font-size: 12px; color: var(--text-muted); margin: 2px 0;">${act.subtitle}</p>
                                <span style="font-size: 13px; font-weight: 600; color: var(--text-main);">${act.amount}</span>
                            </div>
                        </div>
                    `;
                });
            });
    }

    // Auto-update every 2 minutes
    setInterval(loadActivities, 120000);

    function initChart() {
        const chartEl = document.getElementById('salesChart');
        if (!chartEl) return;

        fetch('actions/get_dashboard_analytics.php')
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                if (!data.sales_trend) return;
                
                const ctx = chartEl.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.sales_trend.map(d => d.day),
                        datasets: [{
                            label: 'Revenue Trend (' + (typeof window.CURRENCY_SYMBOL !== 'undefined' ? window.CURRENCY_SYMBOL : '₹') + ')',
                            data: data.sales_trend.map(d => d.total),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#6366f1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true,
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                ticks: { color: '#94a3b8', font: { size: 10 } }
                            },
                            x: { 
                                grid: { display: false },
                                ticks: { color: '#94a3b8', font: { size: 10 } }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error("Analytics Error:", err));
    }
    function initTips() {
        const tips = [
            "Keep your high-movers closer to the dispatch zone to minimize travel distance.",
            "Always perform a 'Blind Count' during stocktakes to ensure maximum accuracy.",
            "Group products by velocity (ABC analysis) to optimize warehouse layout.",
            "Cross-docking can significantly reduce handling costs and storage time.",
            "Ensure every SKU has a clear, readable barcode to prevent manual entry errors.",
            "Regular maintenance of pallet jacks and forklifts prevents operational downtime."
        ];
        const tipEl = document.getElementById('logisticsTip');
        if (tipEl) {
            tipEl.innerText = tips[Math.floor(Math.random() * tips.length)];
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>
