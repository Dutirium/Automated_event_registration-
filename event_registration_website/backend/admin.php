<?php
session_start();
require 'config/db.php';
$conn = getDB();


 //  FETCH EVENTS

$eventsStmt = $conn->prepare("SELECT event_id, event_name FROM events ORDER BY event_name ASC");
$eventsStmt->execute();
$eventsList = $eventsStmt->get_result();


 // SELECTED EVENT

$selectedEvent = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

   //SUMMARY STATS 

function fetchCount($conn, $query, $param = null) {
    if ($param !== null) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $param);
    } else {
        $stmt = $conn->prepare($query);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['c'];
}

$totalRegistrations = fetchCount($conn, "SELECT COUNT(*) as c FROM registrations");
$totalCategories    = fetchCount($conn, "SELECT COUNT(*) as c FROM category");
$totalEvents        = fetchCount($conn, "SELECT COUNT(*) as c FROM events");


   //EVENT REGISTRATION STATS

if ($selectedEvent > 0) {

    $totalEventRegs = fetchCount(
        $conn,
        "SELECT COUNT(*) as c FROM event_registrations WHERE event_id = ?",
        $selectedEvent
    );

    $totalVerified = fetchCount(
        $conn,
        "SELECT COUNT(*) as c FROM event_registrations WHERE event_id = ? AND verified = 1",
        $selectedEvent
    );

    $totalUnverified = fetchCount(
        $conn,
        "SELECT COUNT(*) as c FROM event_registrations WHERE event_id = ? AND verified = 0",
        $selectedEvent
    );

} else {

    $totalEventRegs = fetchCount($conn, "SELECT COUNT(*) as c FROM event_registrations");
    $totalVerified  = fetchCount($conn, "SELECT COUNT(*) as c FROM event_registrations WHERE verified = 1");
    $totalUnverified= fetchCount($conn, "SELECT COUNT(*) as c FROM event_registrations WHERE verified = 0");
}


  // FETCH DATA TABLE


if ($selectedEvent > 0) {

    $dataStmt = $conn->prepare("
        SELECT r.participation_id,
               r.name,
               r.email,
               r.email_verified,
               e.event_name,
               c.category_name,
               er.verified,
               er.verified_at
        FROM registrations r
        LEFT JOIN event_registrations er ON r.participation_id = er.participation_id
        LEFT JOIN events e ON er.event_id = e.event_id
        LEFT JOIN category c ON e.category_id = c.category_id
        WHERE er.event_id = ?
        ORDER BY r.created_at DESC
    ");

    $dataStmt->bind_param("i", $selectedEvent);

} else {

    $dataStmt = $conn->prepare("
        SELECT r.participation_id,
               r.name,
               r.email,
               r.email_verified,
               e.event_name,
               c.category_name,
               er.verified,
               er.verified_at
        FROM registrations r
        LEFT JOIN event_registrations er ON r.participation_id = er.participation_id
        LEFT JOIN events e ON er.event_id = e.event_id
        LEFT JOIN category c ON e.category_id = c.category_id
        ORDER BY r.created_at DESC
    ");
}

$dataStmt->execute();
$data = $dataStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>ADMIN_DASHBOARD</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=JetBrains+Mono:wght@300;500&display=swap" rel="stylesheet">
<style>
:root{
--neon-cyan:#00f3ff;
--neon-pink:#ff00ff;
--bg:#050505;
}
body{
margin:0;
font-family:'JetBrains Mono',monospace;
background:var(--bg);
color:white;
}
h1,h2{
font-family:'Orbitron',sans-serif;
color:var(--neon-cyan);
letter-spacing:2px;
}
.container{ padding:30px; }
.card{
background:#0a0a0a;
border:1px solid var(--neon-cyan);
padding:20px;
margin-bottom:20px;
box-shadow:0 0 15px rgba(0,243,255,0.2);
}
.stats{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:20px;
}
.stat{
background:#111;
padding:15px;
border:1px solid var(--neon-pink);
text-align:center;
}
.stat span{
display:block;
font-size:22px;
color:var(--neon-cyan);
}
select{
background:#000;
color:var(--neon-cyan);
border:1px solid var(--neon-pink);
padding:8px;
font-family:'JetBrains Mono';
}
button{
background:var(--neon-cyan);
color:black;
border:none;
padding:8px 15px;
cursor:pointer;
font-weight:bold;
}
table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}
th,td{
padding:10px;
border:1px solid #222;
font-size:13px;
}
th{
background:#111;
color:var(--neon-cyan);
}
tr:nth-child(even){ background:#0c0c0c; }
.verified{ color:var(--neon-cyan); }
.unverified{ color:#ff4d4d; }
</style>
</head>
<body>

<div class="container">

<h1>ADMIN_CONTROL_PANEL</h1>

<div class="card">
<h2>FILTER_BY_EVENT</h2>
<form method="GET">
<select name="event_id">
<option value="0">ALL_EVENTS</option>
<?php while($event = $eventsList->fetch_assoc()): ?>
<option value="<?= $event['event_id'] ?>"
<?= ($selectedEvent == $event['event_id']) ? 'selected' : '' ?>>
<?= strtoupper($event['event_name']) ?>
</option>
<?php endwhile; ?>
</select>
<button type="submit">APPLY_FILTER</button>
</form>
</div>

<div class="stats">
<div class="stat"><span><?= $totalRegistrations ?></span>TOTAL_USERS</div>
<div class="stat"><span><?= $totalCategories ?></span>CATEGORIES</div>
<div class="stat"><span><?= $totalEvents ?></span>EVENTS</div>
<div class="stat"><span><?= $totalEventRegs ?></span>EVENT_REGISTRATIONS</div>
<div class="stat"><span><?= $totalVerified ?></span>VERIFIED</div>
<div class="stat"><span><?= $totalUnverified ?></span>NOT_VERIFIED</div>
</div>

<div class="card">
<h2>REGISTRATION_DATA_STREAM</h2>
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Email Verified</th>
<th>Event</th>
<th>Category</th>
<th>Attendance</th>
<th>Verified At</th>
</tr>

<?php while($row = $data->fetch_assoc()): ?>
<tr>
<td><?= $row['participation_id'] ?></td>
<td><?= strtoupper($row['name']) ?></td>
<td><?= $row['email'] ?></td>
<td><?= $row['email_verified'] ? 'YES' : 'NO' ?></td>
<td><?= $row['event_name'] ?></td>
<td><?= $row['category_name'] ?></td>
<td class="<?= $row['verified'] ? 'verified' : 'unverified' ?>">
<?= $row['verified'] ? 'VERIFIED' : 'PENDING' ?>
</td>
<td><?= $row['verified_at'] ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>

</div>
</body>
</html>