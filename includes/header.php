<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> | Warehouse System</title>
    <!-- Use absolute path relative to domain to ensure it works in subdirectories -->
    <link rel="stylesheet" href="/warehouse/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <link rel="manifest" href="/warehouse/manifest.json">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/warehouse/sw.js');
        }
        // Apply theme immediately to prevent flashing
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
</head>
<body>
    <script>
        const CURRENCY_SYMBOL = '<?php echo CURRENCY_SYMBOL; ?>';
    </script>
    <div class="dashboard-container">
        <?php include 'top_nav.php'; ?>
