<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require __DIR__ . "/config/db.php";
$conn = getDB();



$token = $_POST['token'] ?? null;
$event_id = $_POST['event_id'] ?? null;

if (!$token || !$event_id) {
    echo json_encode([
        "status" => "invalid",
        "error" => "Missing token or event_id"
    ]);
    exit;
}


$hash = hash("sha256", $token);


$stmt = $conn->prepare(
    "SELECT participation_id, name 
     FROM registrations 
     WHERE qr_hash = ?"
);

$stmt->bind_param("s", $hash);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "invalid",
        "message" => "QR not recognized"
    ]);
    exit;
}

$row = $result->fetch_assoc();
$participation_id = $row['participation_id'];
$name = $row['name'];



$update = $conn->prepare(
    "UPDATE event_registrations
     SET verified = 1,
         verified_at = NOW()
     WHERE participation_id = ?
     AND event_id = ?
     AND verified = 0"
);

$update->bind_param("ii", $participation_id, $event_id);
$update->execute();

if ($update->affected_rows === 1) {
    echo json_encode([
        "status" => "success",
        "name" => $name,
        "message" => "Verification successful"
    ]);
} else {

    // Check if already verified
    $check = $conn->prepare(
        "SELECT verified FROM event_registrations
         WHERE participation_id = ?
         AND event_id = ?"
    );

    $check->bind_param("ii", $participation_id, $event_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        echo json_encode([
            "status" => "not_registered",
            "name" => $name,
            "message" => "Not registered for this event"
        ]);
    } else {
        echo json_encode([
            "status" => "already",
            "name" => $name,
            "message" => "Already Checked In!!"
        ]);
    }

    $check->close();
}

$stmt->close();
$update->close();
$conn->close();