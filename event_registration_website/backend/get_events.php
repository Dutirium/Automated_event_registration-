<?php
require __DIR__.'/config/db.php';

$conn=getDB();

$category_id = $_GET['category_id'];

$stmt = $conn->prepare("SELECT event_id, event_name FROM events WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

?>