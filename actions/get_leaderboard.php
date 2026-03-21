<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) exit();

$query = "SELECT username, xp, role FROM users ORDER BY xp DESC LIMIT 5";
$res = $conn->query($query);
$leaderboard = [];
while($row = $res->fetch_assoc()) {
    $leaderboard[] = $row;
}

echo json_encode($leaderboard);
?>
