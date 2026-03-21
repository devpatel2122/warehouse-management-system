<?php
/**
 * Task Management Actions
 */
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'add_task') {
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $assigned_to = intval($_POST['assigned_to']);
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $xp_reward = intval($_POST['xp_reward'] ?? 10);

    $stmt = $conn->prepare("INSERT INTO tasks (title, description, assigned_to, priority, due_date, xp_reward) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissi", $title, $desc, $assigned_to, $priority, $due_date, $xp_reward);
    
    if ($stmt->execute()) {
        logActivity("New Task Created", "tasks", $conn->insert_id, $title);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

} elseif ($action === 'update_status') {
    $task_id = intval($_POST['task_id']);
    $status = $_POST['status'];

    // If completed, award XP
    if ($status === 'Completed') {
        $stmt = $conn->prepare("SELECT assigned_to, xp_reward, status FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $task = $stmt->get_result()->fetch_assoc();

        if ($task && $task['status'] !== 'Completed') {
            $uid = $task['assigned_to'];
            $xp = $task['xp_reward'];
            $conn->query("UPDATE users SET xp = xp + $xp WHERE id = $uid");
            logActivity("Task Completed", "tasks", $task_id, "Awarded $xp XP");
        }
    }

    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $task_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
