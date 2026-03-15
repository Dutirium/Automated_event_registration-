<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require __DIR__ . "/config/db.php";
$conn = getDB();



$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    echo json_encode([
        "error" => "Missing event_id"
    ]);
    exit;
}

//find verified students

$verified_stmt = $conn->prepare(
    "SELECT r.name, er.verified_at
     FROM event_registrations er
     JOIN registrations r 
       ON er.participation_id = r.participation_id
     WHERE er.event_id = ?
       AND er.verified = 1
     ORDER BY er.verified_at DESC"
);

$verified_stmt->bind_param("i", $event_id);
$verified_stmt->execute();
$verified_result = $verified_stmt->get_result();

$verified = [];

while ($row = $verified_result->fetch_assoc()) {
    $verified[] = $row;
}


// find absent students
$absent_stmt = $conn->prepare(
    "SELECT r.name
     FROM event_registrations er
     JOIN registrations r 
       ON er.participation_id = r.participation_id
     WHERE er.event_id = ?
       AND er.verified = 0
     ORDER BY r.name ASC"
);

$absent_stmt->bind_param("i", $event_id);
$absent_stmt->execute();
$absent_result = $absent_stmt->get_result();

$absent = [];

while ($row = $absent_result->fetch_assoc()) {
    $absent[] = $row;
}

//return json

echo json_encode([
    "verified" => $verified,
    "absent" => $absent
]);

$verified_stmt->close();
$absent_stmt->close();
$conn->close();

?>