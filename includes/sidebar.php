<?php
$role = $_SESSION['role'];
$site_root = '/warehouse/';
?>
<aside class="sidebar">
    <div class="sidebar-brand mb-4">
        <!-- Reverting to original branding as per user request -->
        <h2 style="font-size: 20px; color: var(--primary); font-weight: 700;">WAREHOUSE PRO</h2>
    </div>
    
    <nav class="sidebar-nav">
        <ul style="list-style: none;">
            <li class="mb-4">
                <a href="<?php echo $site_root; ?>dashboard.php" class="nav-link" style="text-decoration: none; color: var(--text-main); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>

            <?php if ($role == 'admin') : ?>
            <li class="mb-4">
                <p style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 12px;">Admin</p>
                <a href="<?php echo $site_root; ?>modules/admin/departments.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-users-cog"></i> Departments
                </a>
                <a href="<?php echo $site_root; ?>modules/admin/reports.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="<?php echo $site_root; ?>modules/admin/calendar.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-calendar-alt"></i> Calendar
                </a>
                <a href="<?php echo $site_root; ?>modules/admin/settings.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-cogs"></i> System Settings
                </a>
                <a href="<?php echo $site_root; ?>modules/admin/audit.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-shield-alt"></i> Security Audit
                </a>
            </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'product_dept') : ?>
            <li class="mb-4">
                <!-- Using Product name as requested -->
                <p style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 12px;">Product</p>
                <a href="<?php echo $site_root; ?>modules/product/categories.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="<?php echo $site_root; ?>modules/product/sub_categories.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-layer-group"></i> Sub-Categories
                </a>
                <a href="<?php echo $site_root; ?>modules/product/inventory.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-boxes"></i> Products
                </a>
                <a href="<?php echo $site_root; ?>modules/product/import.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-file-import"></i> Bulk Import
                </a>
            </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'purchase_dept') : ?>
            <li class="mb-4">
                <p style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 12px;">Purchase</p>
                <a href="<?php echo $site_root; ?>modules/purchase/vendors.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-truck"></i> Vendors
                </a>
                <a href="<?php echo $site_root; ?>modules/purchase/orders.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-shopping-cart"></i> Buy Products
                </a>
            </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'sell_dept') : ?>
            <li class="mb-4">
                <p style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 12px;">Sell</p>
                <a href="<?php echo $site_root; ?>modules/sell/customers.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-user-friends"></i> Customers
                </a>
                <a href="<?php echo $site_root; ?>modules/sell/sales.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-cash-register"></i> Manage Sell
                </a>
                <a href="<?php echo $site_root; ?>modules/sell/invoices.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-file-invoice"></i> Manage Invoice
                </a>
            </li>
            <?php endif; ?>

            <?php if ($role == 'admin' || $role == 'inventory_dept') : ?>
            <li class="mb-4">
                <p style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 12px;">Stock</p>
                <a href="<?php echo $site_root; ?>modules/inventory/stock.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-warehouse"></i> Inventory Status
                </a>
                <a href="<?php echo $site_root; ?>modules/inventory/floor_map.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-map"></i> Digital Floor Map
                </a>
            </li>
            <?php endif; ?>
            <?php if ($role == 'admin' || $role == 'sell_dept') : ?>
            <li class="mb-4">
                <p style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 12px;">Returns</p>
                <a href="<?php echo $site_root; ?>modules/returns/manage.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-undo"></i> Returns Management
                </a>
            </li>
            <?php endif; ?>

            <li class="mb-4">
                <p style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 12px;">Operations</p>
                <a href="<?php echo $site_root; ?>modules/tasks/manage.php" class="nav-link" style="text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px;">
                    <i class="fas fa-tasks"></i> Task Board
                </a>
            </li>

            <li class="mt-auto" style="margin-top: auto; padding-top: 40px;">
                <a href="<?php echo $site_root; ?>actions/logout.php" class="nav-link" style="text-decoration: none; color: #ff4757; display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 12px; background: rgba(255, 71, 87, 0.05); border: 1px solid rgba(255, 71, 87, 0.1);">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<script>
// Prevent sidebar scroll reset on navigation
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        // Restore scroll position
        const scrollPos = localStorage.getItem('sidebarScroll');
        if (scrollPos) {
            sidebar.scrollTop = parseInt(scrollPos);
        }

        // Save scroll position before navigation
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                localStorage.setItem('sidebarScroll', sidebar.scrollTop);
            });
        });
    }
});
</script>

<style>
/* Added a bit of a transition here to make it feel smoother */
.nav-link {
    transition: all 0.2s ease-in-out;
}
.nav-link:hover {
    background: var(--glass-bg);
    color: var(--text-main) !important;
}
.sidebar::-webkit-scrollbar {
    display: none; /* Hide scrollbar for a cleaner Look while keeping functionality */
}
.sidebar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
