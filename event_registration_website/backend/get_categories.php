<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require __DIR__ . "/config/db.php";
$conn = getDB();

//this is pretty obvious 

$stmt = $conn->prepare(
    "SELECT category_id, category_name FROM category ORDER BY category_name"
);

if (!$stmt) {
    echo json_encode([
        "error" => $conn->error
    ]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();

?>