<?php
$page_title = 'Operational Calendar';
require_once '../../includes/db.php';
$base_path = '../../';

if ($_SESSION['role'] != 'admin') {
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
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <!-- FullCalendar CDN -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        :root {
            --fc-border-color: rgba(255, 255, 255, 0.1);
            --fc-daygrid-event-dot-width: 8px;
            --fc-page-bg-color: transparent;
        }
        .fc { background: var(--card-bg); padding: 20px; border-radius: 24px; border: 1px solid var(--border-color); color: var(--text-main); }
        .fc-theme-standard td, .fc-theme-standard th { border-color: var(--fc-border-color); }
        .fc-col-header-cell { padding: 10px 0; background: var(--glass-bg); }
        .fc-day-today { background: rgba(99, 102, 241, 0.05) !important; }
        .fc-button-primary { background: var(--primary) !important; border: none !important; }
        .fc-button-primary:hover { background: var(--primary-hover) !important; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include $base_path . 'includes/sidebar.php'; ?>
        <?php include $base_path . 'includes/top_nav.php'; ?>

        <main class="main-content">
            <header class="mb-4">
                <h1 style="font-size: 24px; font-weight: 700;">Operational Calendar</h1>
                <p style="color: var(--text-muted);">View all sales and procurement activities in a timeline.</p>
            </header>

            <div id='calendar'></div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: '../../actions/get_calendar_events.php'
            });
            calendar.render();
        });
    </script>
</body>
</html>
