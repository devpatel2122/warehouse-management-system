<?php
$page_title = 'Task Management';
require_once '../../includes/db.php';
$base_path = '../../';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'index.php');
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Fetch Users for assignment
$users_res = $conn->query("SELECT id, username FROM users");
$system_users = [];
while($u = $users_res->fetch_assoc()) $system_users[] = $u;

// Fetch Tasks
if ($role === 'admin') {
    $tasks_res = $conn->query("SELECT t.*, u.username as assigned_name FROM tasks t LEFT JOIN users u ON t.assigned_to = u.id ORDER BY t.created_at DESC");
} else {
    $tasks_res = $conn->query("SELECT t.*, u.username as assigned_name FROM tasks t LEFT JOIN users u ON t.assigned_to = u.id WHERE t.assigned_to = $user_id ORDER BY t.created_at DESC");
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
</head>
<body>
    <div class="dashboard-container">
        <?php include $base_path . 'includes/sidebar.php'; ?>
        <?php include $base_path . 'includes/top_nav.php'; ?>

        <main class="main-content">
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700;">Task Board</h1>
                    <p style="color: var(--text-muted);">Coordinate operations and earn XP rewards.</p>
                </div>
                <?php if ($role === 'admin') : ?>
                <button class="btn" onclick="document.getElementById('taskModal').style.display='flex'" style="width: auto;">
                    <i class="fas fa-plus"></i> Create Task
                </button>
                <?php endif; ?>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Priority</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>XP Reward</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tasks_res->num_rows > 0) : ?>
                            <?php while($task = $tasks_res->fetch_assoc()) : ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($task['title']); ?></div>
                                    <div style="font-size: 11px; color: var(--text-muted);">Due: <?php echo $task['due_date']; ?></div>
                                </td>
                                <td>
                                    <?php 
                                    $p_class = strtolower($task['priority']);
                                    echo "<span class='badge' style='background:rgba(var(--primary-rgb), 0.1);'>{$task['priority']}</span>";
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($task['assigned_name'] ?? 'Unassigned'); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo ($task['status'] === 'Completed' ? 'success' : ($task['status'] === 'In-Progress' ? 'warning' : 'danger')); ?>">
                                        <?php echo $task['status']; ?>
                                    </span>
                                </td>
                                <td style="color: var(--primary); font-weight: 700;">+<?php echo $task['xp_reward']; ?> XP</td>
                                <td>
                                    <?php if ($task['status'] !== 'Completed') : ?>
                                        <select onchange="updateTaskStatus(<?php echo $task['id']; ?>, this.value)" class="form-input" style="padding: 4px 8px; font-size: 12px; width: 120px;">
                                            <option value="Pending" <?php if($task['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="In-Progress" <?php if($task['status']=='In-Progress') echo 'selected'; ?>>In-Progress</option>
                                            <option value="Completed">Complete</option>
                                        </select>
                                    <?php else : ?>
                                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">No active tasks found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Task Modal -->
    <div id="taskModal" class="modal">
        <div class="auth-card" style="max-width: 500px;">
            <h2 style="margin-bottom: 20px;">Create New Task</h2>
            <form id="taskForm">
                <input type="hidden" name="action" value="add_task">
                <div class="form-group">
                    <label>Task Title</label>
                    <input type="text" name="title" class="form-input" required placeholder="e.g. Audit Electronics Aisle">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-input" style="height: 80px;"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Assign To</label>
                        <select name="assigned_to" class="form-input">
                            <?php foreach($system_users as $u) : ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo $u['username']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" class="form-input">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>XP Reward</label>
                        <input type="number" name="xp_reward" class="form-input" value="10">
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn" style="background: var(--secondary);" onclick="document.getElementById('taskModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn">Create Task</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('<?php echo $base_path; ?>actions/task_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) location.reload();
                    else alert(data.message);
                });
        });

        function updateTaskStatus(taskId, status) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('task_id', taskId);
            formData.append('status', status);

            fetch('<?php echo $base_path; ?>actions/task_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) location.reload();
                });
        }
    </script>
</body>
</html>
