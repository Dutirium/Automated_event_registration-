<?php
session_start();
require __DIR__.'/config/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$error = '';
$conn = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
  
        $stmt = $conn->prepare("SELECT * FROM admin WHERE name = ? AND pass = ?");
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['admin'] = $username;
            header("Location: admin.php");
            exit;
        } else {
            $error = "ACCESS_DENIED: INVALID_CREDENTIALS";
        }
    } else {
        $error = "REQUIRED_FIELDS_EMPTY";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN_LOGIN_GATEWAY</title>
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
            margin: 0; padding: 0;
            background-color: var(--bg-black);
            color: white;
            font-family: var(--font-main);
            display: flex; justify-content: center; align-items: center;
            height: 100vh; overflow: hidden;
        }

        .grid-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: linear-gradient(rgba(0, 243, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0, 243, 255, 0.03) 1px, transparent 1px);
            background-size: 30px 30px; z-index: -1;
        }

        .scanlines {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%);
            background-size: 100% 4px; pointer-events: none; z-index: 10;
        }

        .login-container {
            background: rgba(10, 10, 10, 0.9);
            border: 1px solid var(--neon-cyan);
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.2);
            padding: 40px;
            width: 100%; max-width: 400px;
            position: relative;
            clip-path: polygon(0 0, 90% 0, 100% 10%, 100% 100%, 10% 100%, 0 90%);
        }

        .login-container::before {
            content: "SECURE_GATEWAY_V4.0";
            position: absolute; top: -10px; left: 20px;
            background: var(--bg-black);
            padding: 0 10px; font-size: 0.6rem; color: var(--neon-cyan);
            letter-spacing: 2px;
        }

        h2 {
            font-family: var(--font-header);
            color: var(--neon-pink);
            text-align: center;
            font-size: 1.2rem;
            letter-spacing: 3px;
            margin-bottom: 30px;
            text-shadow: 0 0 10px var(--neon-pink);
        }

        .input-group { margin-bottom: 20px; position: relative; }

        input {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(0, 243, 255, 0.2);
            padding: 15px;
            color: var(--neon-cyan);
            font-family: var(--font-main);
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }

        input:focus {
            border-color: var(--neon-cyan);
            background: rgba(0, 243, 255, 0.05);
            box-shadow: 0 0 10px rgba(0, 243, 255, 0.2);
        }

        .error-msg {
            color: var(--neon-pink);
            font-size: 0.7rem;
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
            border: 1px solid var(--neon-pink);
            padding: 10px;
            background: rgba(255, 0, 255, 0.05);
        }

        .login-btn {
            width: 100%;
            background: var(--neon-cyan);
            color: black;
            border: none;
            padding: 15px;
            font-family: var(--font-header);
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            clip-path: polygon(5% 0, 100% 0, 100% 70%, 95% 100%, 0 100%, 0 30%);
        }

        .login-btn:hover {
            filter: brightness(1.2);
            box-shadow: 0 0 15px var(--neon-cyan);
        }

        .footer-tag {
            text-align: center; font-size: 0.5rem; color: #444;
            margin-top: 25px; letter-spacing: 2px;
        }
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="scanlines"></div>

<div class="login-container">
    <h2>ADMIN_LOGIN</h2>

    <?php if ($error): ?>
        <div class="error-msg">> ERROR: <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="username" placeholder="OPERATOR_ID" required autofocus>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="ACCESS_KEY" required>
        </div>
        <button type="submit" class="login-btn">AUTHORIZE_SESSION</button>
    </form>

    <div class="footer-tag">ENCRYPTION_LAYER_6_ACTIVE</div>
</div>

</body>
</html>