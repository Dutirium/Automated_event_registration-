<?php
session_start();

// for displaying errors dont use in live mode 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$_SESSION['email']=$email;

// flow control this is necessary but not in development
// if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
//     header("Location: ../index.php");
//     exit();
// }



if (isset($_POST['add_more'])) {
    header("Location: selection_advanced.php");
    exit();
}

if (isset($_POST['destroy_session'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit();
}

$email = $_SESSION['authenticated_email'] ?? '';
$name  = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EVENT_CONTROL_PANEL</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=JetBrains+Mono:wght@300;500&display=swap" rel="stylesheet">

<style>
:root {
--neon-cyan: #00f3ff;
--neon-pink: #ff00ff;
--bg-black: #050505;
--font-main: 'JetBrains Mono', monospace;
--font-header: 'Orbitron', sans-serif;
}

body {
margin:0;
padding:20px;
background:var(--bg-black);
color:white;
font-family:var(--font-main);
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
}

.grid-bg {
position: fixed; top: 0; left: 0; width: 100%; height: 100%;
background-image:
linear-gradient(rgba(0,243,255,0.05) 1px, transparent 1px),
linear-gradient(90deg, rgba(0,243,255,0.05) 1px, transparent 1px);
background-size: 30px 30px;
z-index:-1;
}

.cyber-card {
background: rgba(10,10,10,0.95);
border:2px solid var(--neon-cyan);
box-shadow:0 0 20px rgba(0,243,255,0.2);
padding:40px;
max-width:450px;
width:100%;
clip-path: polygon(0% 0%, 90% 0%, 100% 10%, 100% 100%, 10% 100%, 0% 90%);
}

h2 {
font-family:var(--font-header);
color:var(--neon-cyan);
margin-bottom:20px;
}

.info {
margin-bottom:25px;
font-size:0.85rem;
color:#aaa;
}

.neon-cyan {
color:var(--neon-cyan);
}

.cyber-btn {
width:100%;
background:var(--neon-cyan);
color:black;
border:none;
padding:16px;
font-family:var(--font-header);
font-weight:bold;
cursor:pointer;
margin-top:15px;
clip-path: polygon(10% 0%, 100% 0%, 100% 70%, 90% 100%, 0% 100%, 0% 30%);
transition:0.3s;
}

.cyber-btn:hover {
background:var(--neon-pink);
box-shadow:0 0 15px var(--neon-pink);
}

.danger:hover {
background:var(--neon-pink);
color:black;
}
</style>
</head>

<body>

<div class="grid-bg"></div>

<div class="cyber-card">

<h2>EVENT_MANAGEMENT_NODE</h2>

<div class="info">
AUTHENTICATED_USER:
<br>
<span class="neon-cyan"><?php echo htmlspecialchars($email); ?></span>
</div>

<form method="POST">
<button type="submit" name="add_more" class="cyber-btn">
ADD_MORE_EVENTS
</button>
</form>


<button type="submit" name="destroy_session" class="cyber-btn danger" onclick="window.location.href='logout.php';">
TERMINATE_SESSION_&_RETURN
</button>

</div>

</body>
</html>