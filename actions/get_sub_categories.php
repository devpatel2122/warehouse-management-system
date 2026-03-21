<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$category_id = intval($_GET['category_id'] ?? 0);
$where = $category_id > 0 ? "WHERE s.category_id = $category_id" : "";

$query = "SELECT s.*, c.name as category_name 
          FROM sub_categories s 
          JOIN categories c ON s.category_id = c.id 
          $where
          ORDER BY s.id ASC";

$result = $conn->query($query);
$subs = [];
while ($row = $result->fetch_assoc()) {
    $subs[] = $row;
}
echo json_encode($subs);
?>
