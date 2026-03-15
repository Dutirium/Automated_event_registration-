<?php 
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$status = null;
$advanced_status = null;
$email = $_SESSION['email'];
$name = $_SESSION['name'];
$event_id = $_SESSION['event_id'];
$category_id = $_SESSION['category_id'];

require 'config/db.php';
require '../vendor/autoload.php';
require 'config/mail.php';

$conn = getDB();
$mail = getMailer();

// INSERT REGISTRATION
if(!(isset($_SESSION["remail"]) && $_SESSION['remail']!==null))
{
while (true) {

    $token = bin2hex(random_bytes(32));
    $hash = hash("sha256", $token);

    $stmt = $conn->prepare("INSERT INTO registrations(name,email,qr_hash,email_verified) VALUES(?,?,?,1)");
    $stmt->bind_param('sss', $name, $email, $hash);

    if ($stmt->execute()) {
        break;
    } else {
        if ($conn->errno == 1062) {
            continue;
        } else {
            die("Database error: " . $conn->error);
        }
    }
}
}



// GET PARTICIPATION ID
$stmt = $conn->prepare("SELECT participation_id FROM registrations WHERE email=?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $participation_id = $row['participation_id'];
}

$_SESSION['participation_id'] = $participation_id;

//INSERT EVENT REGISTRATIONS
try {

    $stmt = $conn->prepare("INSERT INTO event_registrations(event_id,participation_id,category_id) VALUES(?,?,?)");
    $stmt->bind_param('iii', $e_id, $participation_id, $c_id);

    for ($i = 0; $i < count($event_id); $i++) {
        $e_id = $event_id[$i];
        $c_id = $category_id[$i];
        $stmt->execute();
    }
    $advanced_status="success";

} catch (mysqli_sql_exception $e) {

    if ($e->getCode() === 1062) {
        $status = "DUPLICATE";
    } else {
        $status = "ERROR";
    }
}

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
if(!(isset($_SESSION["remail"]) && $_SESSION['remail']!==null)){

$builder = new Builder(
    writer: new PngWriter(),
    data: $token,
    size: 300,
    margin: 10
);

$result = $builder->build();
$qrImageString = $result->getString();


$mail->addAddress($email);
$mail->isHTML(true);
$mail->Subject = 'Your Event Entry QR Code';


$mail->Body = "
    <h1>Hello,</h1>
    <p>Thank you for registering for our event! Below is your unique entry QR code:</p>
    <p><img src='cid:qrimg' alt='Your QR Code' style='width:200px; height:200px;'></p>
    <p>Please have this ready at the entrance.</p>
    <br>
    <p>Best regards,<br>The GJ Event Team</p>
";


$mail->AltBody = "Hello, thank you for registering. Your QR code is attached to this email.";


$mail->addStringEmbeddedImage(
    $qrImageString,
    'qrimg',
    'qrcode.png',
    'base64',
    'image/png'
);

$mail->send();

$mail->Body = "
<div style='font-family: Arial, sans-serif; text-align:center;'>
    <h2>Your Entry Pass</h2>
    <p>Please present this QR code at the venue.</p>
    <img src='cid:qrimg' style='width:220px; height:auto;'/>
</div>
";

if ($mail->send()) {
    $status = "SUCCESS";
} else {
    $status = "MAIL_ERROR";
}

}

?>
<!DOCTYPE html>
<html>
<head>
<title>FINALIZING_REGISTRATION</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=JetBrains+Mono:wght@300;500&display=swap" rel="stylesheet">
</head>
<body>

<div class="grid-bg"></div>
<div class="scanlines"></div>

<div class="container">
    <div class="cyber-card">
        <div class="card-header">
            <span class="status-dot"></span>
            <h2>REGISTRATION_PROCESS</h2>
        </div>

        <div id="terminal" class="terminal-output"></div>
        <div class="loader"></div>
    </div>
</div>

<style>
:root {
    --neon-cyan: #00f3ff;
    --bg-black: #050505;
    --font-main: 'JetBrains Mono', monospace;
    --font-header: 'Orbitron', sans-serif;
}

body {
    margin: 0;
    background: var(--bg-black);
    font-family: var(--font-main);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.grid-bg {
    position: fixed;
    width: 100%;
    height: 100%;
    background-image:
        linear-gradient(rgba(0,243,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,243,255,0.05) 1px, transparent 1px);
    background-size: 30px 30px;
    z-index: -1;
}

.scanlines {
    position: fixed;
    width: 100%;
    height: 100%;
    background: linear-gradient(rgba(0,0,0,0) 50%, rgba(0,0,0,0.25) 50%);
    background-size: 100% 4px;
    pointer-events: none;
}

.cyber-card {
    background: rgba(10,10,10,0.95);
    border: 2px solid var(--neon-cyan);
    box-shadow: 0 0 25px rgba(0,243,255,0.3);
    padding: 40px;
    width: 500px;
    clip-path: polygon(0% 0%, 90% 0%, 100% 10%, 100% 100%, 10% 100%, 0% 90%);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: var(--neon-cyan);
    border-radius: 50%;
    box-shadow: 0 0 8px var(--neon-cyan);
    animation: blink 1s infinite;
}

h2 {
    font-family: var(--font-header);
    font-size: 1rem;
    color: var(--neon-cyan);
    margin: 0;
}

.terminal-output {
    font-size: 0.85rem;
    min-height: 120px;
    white-space: pre-line;
    color: var(--neon-cyan);
}

.loader {
    margin-top: 20px;
    width: 40px;
    height: 40px;
    border: 3px solid rgba(0,243,255,0.2);
    border-top: 3px solid var(--neon-cyan);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }
@keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }
</style>

<script>
const steps = [
"> VALIDATING_PAYMENT...",
"> INSERTING_REGISTRATION...",
"> LINKING_EVENTS...",
"> GENERATING_QR_TOKEN...",
"> SENDING_EMAIL...",
"> FINALIZING_PASS..."
];

let i = 0;
const terminal = document.getElementById("terminal");

function process() {
    if (i < steps.length) {
        terminal.innerHTML += steps[i] + "\n";
        i++;
        setTimeout(process, 500);
    } else {
        setTimeout(() => {
            <?php if ($status === "SUCCESS" || $advanced_status=== "success" ): ?>
                window.location.href = "receipt.php";
            <?php else: ?>
                terminal.innerHTML += "\n> ERROR: PROCESS_FAILED";
            <?php endif; ?>
        }, 1000);
    }
}

process();
</script>

</body>
</html>