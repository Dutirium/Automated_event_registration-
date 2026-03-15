<?php
date_default_timezone_set('Asia/Kolkata');
session_start();

require 'config/db.php';
require '../vendor/autoload.php';
require 'config/mail.php';

use PHPMailer\PHPMailer\PHPMailer;

if(isset($_SESSION["verified"]) && $_SESSION["verified"]===true)
    {
        header("Location:selection.php");
        exit();

    }

$message = "";
$mail=getMailer();
$conn= getDB();

if (isset($_POST['send_otp'])) {
    $email = ($_POST['email'])?$_POST['email']:null;
    $name = ($_POST['name'])?$_POST['name']:null;

    //Check for duplicate entry before hand 
    try{
    $stmt=$conn->prepare("SELECT * FROM registrations WHERE email=?");
    $stmt->bind_param('s',$email);
    if($stmt->execute())
        {
            $result = $stmt->get_result();
            if($result->num_rows>0)
                {
                    $_SESSION['verified']=true;

                    $_SESSION['remail']=$email;
                    $_SESSION['rname']=$name;

                    header("Location: add_events.php");
                    exit();
                }
        }
    }
    catch(Exception $e){}


    $otp = random_int(100000, 999999);
    $otp_hash = hash('sha256', $otp);
    $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    $stmt = $conn->prepare("DELETE FROM email_otps WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO email_otps (email, otp_hash, expire_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $otp_hash, $expires_at);
    $stmt->execute();

    $mail->addAddress($email);
    $mail->Subject = 'Email Verification OTP';
    $mail->Body = "Your OTP is: $otp";

    if ($mail->send()) {
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
        $message = "OTP sent successfully.";
      
    } else {
        $message = "Failed to send OTP.";
    }
}

if (isset($_POST['verify_otp'])) {
    $email = $_SESSION['email'];
    $user_otp = $_POST['otp'];
    $user_otp_hash = hash('sha256', $user_otp);

    $stmt = $conn->prepare(
        "SELECT * FROM email_otps 
        WHERE email=? AND otp_hash=? AND expire_at > NOW()"
    );
    $stmt->bind_param("ss", $email, $user_otp_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $del = $conn->prepare("DELETE FROM email_otps WHERE email=?");
        $del->bind_param("s", $email);
        $del->execute();

        $_SESSION['email']=$email;
        $_SESSION['verified']=true;

        
        echo "<script>window.location.href = 'selection.php';</script>";
        exit();
    } else {
        $message = "Invalid or expired OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SYSTEM ACCESS: VERIFY</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=JetBrains+Mono:wght@300;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-cyan: #00f3ff;
            --neon-pink: #ff00ff;
            --bg-black: #050505;
            --font-main: 'JetBrains Mono', monospace;
            --font-header: 'Orbitron', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0; padding: 20px;
            font-family: var(--font-main);
            background-color: var(--bg-black);
            color: white;
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh; overflow-x: hidden;
        }

        /* Background Layers */
        .grid-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: linear-gradient(rgba(0, 243, 255, 0.05) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0, 243, 255, 0.05) 1px, transparent 1px);
            background-size: 30px 30px; z-index: -1;
        }

        .scanlines {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), 
                        linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
            background-size: 100% 4px, 3px 100%; pointer-events: none; z-index: 10;
        }

        .container { width: 100%; display: flex; justify-content: center; z-index: 2; }

        .cyber-card {
            background: rgba(10, 10, 10, 0.95);
            border: 2px solid var(--neon-cyan);
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.2);
            padding: clamp(20px, 5vw, 40px);
            width: 100%; max-width: 450px;
            position: relative;
            clip-path: polygon(0% 0%, 90% 0%, 100% 10%, 100% 100%, 10% 100%, 0% 90%);
        }

        .card-header {
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid rgba(0, 243, 255, 0.3);
            margin-bottom: 20px; padding-bottom: 10px;
        }

        .status-dot {
            width: 8px; height: 8px; background: var(--neon-cyan);
            border-radius: 50%; box-shadow: 0 0 8px var(--neon-cyan);
            animation: blink 1s infinite;
        }

        h2 { font-family: var(--font-header); font-size: clamp(0.9rem, 4vw, 1.1rem); color: var(--neon-cyan); margin: 0; }

        .terminal-text {
            font-size: 0.8rem;
            color: #aaa;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        /* Responsive Typewriter */
        .typewriter {
            overflow: hidden;
            border-right: .15em solid var(--neon-pink);
            white-space: normal; /* Changed for mobile wrap */
            letter-spacing: .05em;
            animation: blink-caret .75s step-end infinite;
        }

        .terminal-msg { font-size: 0.85rem; margin-bottom: 20px; font-weight: bold; word-wrap: break-word; }
        .neon-cyan { color: var(--neon-cyan); text-shadow: 0 0 5px var(--neon-cyan); }
        .neon-red { color: #ff4d4d; text-shadow: 0 0 5px #ff4d4d; }

        .input-wrapper { margin-bottom: 15px; }
        .input-wrapper label {
            display: block; font-size: 0.7rem; color: var(--neon-cyan);
            margin-bottom: 8px; font-weight: bold;
            letter-spacing: 1px;
        }

        input {
            width: 100%; background: #000; border: 1px solid #333;
            padding: 15px; color: var(--neon-cyan); font-family: var(--font-main);
            outline: none; transition: 0.2s;
            font-size: 1rem; /* Better for mobile zoom prevention */
        }

        input:focus { border-color: var(--neon-pink); box-shadow: 0 0 10px rgba(255, 0, 255, 0.2); }

        #otp-input { 
            text-align: center; 
            font-size: 1.5rem; 
            letter-spacing: clamp(5px, 4vw, 10px); 
            border-style: dashed; 
        }

        .cyber-btn {
            width: 100%; background: var(--neon-cyan); color: black;
            border: none; padding: 18px; font-family: var(--font-header);
            font-weight: bold; cursor: pointer;
            clip-path: polygon(10% 0%, 100% 0%, 100% 70%, 90% 100%, 0% 100%, 0% 30%);
            transition: 0.3s;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .cyber-btn:active { transform: scale(0.98); }

        .neon-link {
            color: var(--neon-pink); text-decoration: none; font-size: 0.75rem;
            display: block; text-align: center; margin-top: 20px; opacity: 0.8;
            padding: 10px;
        }

        @keyframes blink-caret { from, to { border-color: transparent } 50% { border-color: var(--neon-pink); } }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        /* Mobile specific adjustments */
        @media (max-width: 480px) {
            .cyber-card { clip-path: none; border-radius: 2px; } /* Simpler shape for tiny screens */
            .cyber-btn { clip-path: none; border-radius: 2px; }
        }

        .alert-box {
    background: rgba(255, 0, 255, 0.05);
    border: 1px solid var(--neon-pink);
    color: var(--neon-pink);
    padding: 15px;
    margin: 20px 0;
    font-family: var(--font-main);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    position: relative;
    clip-path: polygon(0 0, 95% 0, 100% 25%, 100% 100%, 5% 100%, 0 75%);
    box-shadow: 0 0 15px rgba(255, 0, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.alert-box::before {
    content: "!";
    font-weight: bold;
    font-family: var(--font-header);
    border: 1px solid var(--neon-pink);
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 5px var(--neon-pink);
}
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="scanlines"></div>

<div class="container">
    <div class="cyber-card">
        <div class="card-header">
            <span class="status-dot"></span>
            <h2>IDENTITY_VERIFICATION</h2>
        </div>
        
        <?php if ($message): ?>
            <div class="terminal-msg decoding <?php echo strpos($message, 'successfully') !== false ? 'neon-cyan' : 'neon-red'; ?>">
                > [SYSTEM]: <?php echo strtoupper($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['email'])) { ?>
            <p class="terminal-text typewriter">INITIATE_HANDSHAKE: Please provide access credentials to generate a secure OTP...</p>
            
            <form method="POST" class="cyber-form">
                <div class="input-wrapper">
                    <label>YOUR_NAME</label>
                    <input type="text" name="name" required placeholder="NIGHT_RUNNER" autocomplete="off">
                </div>
                <div class="input-wrapper">
                    <label>EMAIL</label>
                    <input type="email" name="email" required placeholder="user@netrunner.com">
                </div>
                <button type="submit" name="send_otp" class="cyber-btn">TRANSMIT_DATA</button>
            </form>
        <?php } else { ?>
            <p class="terminal-text typewriter">ENCRYPTED_SIGNAL_ROUTED_TO: <br><span class="neon-cyan" style="word-break: break-all;"><?php echo $_SESSION['email']; ?></span></p>
            
            <form method="POST" class="cyber-form">
                <div class="input-wrapper">
                    <label>ENTER_PASSCODE</label>
                    <input type="tel" name="otp" id="otp-input" required placeholder="000000" maxlength="6" autocomplete="off">
                </div>
                <button type="submit" name="verify_otp" class="cyber-btn glitch-btn">DECRYPT_ACCESS</button>
                <div class="form-footer">
                    <a href="?resend=1" class="neon-link">REQUEST_NEW_SIGNAL</a>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const otpInput = document.getElementById('otp-input');
        if(otpInput) {
            otpInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        }

        const msg = document.querySelector('.decoding');
        if(msg) {
            const originalText = msg.innerText;
            let iterations = 0;
            const interval = setInterval(() => {
                msg.innerText = originalText.split("")
                    .map((char, index) => {
                        if(index < iterations) return originalText[index];
                        return String.fromCharCode(65 + Math.floor(Math.random() * 26));
                    })
                    .join("");
                if(iterations >= originalText.length) clearInterval(interval);
                iterations += 1/3;
            }, 30);
        }
    });
</script>

</body>
</html>