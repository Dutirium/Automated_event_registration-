<?php
session_start();
// Ensure variables exist to avoid errors
$participation_id = $_SESSION['participation_id'] ?? 0;
$total_price = $_SESSION['total_price'] ?? 0;

require __DIR__.'/config/db.php';

$conn = getDB();

$stmt = $conn->prepare("SELECT event_name,price FROM events WHERE event_id IN (SELECT event_id FROM event_registrations WHERE participation_id=?)");
$stmt->bind_param('i', $participation_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRANSACTION_SUCCESS</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=JetBrains+Mono:wght@300;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-green: #00ff9f;
            --neon-cyan: #00f3ff;
            --bg-black: #050505;
            --font-main: 'JetBrains Mono', monospace;
            --font-header: 'Orbitron', sans-serif;
        }

        body {
            margin: 0; padding: 15px;
            background-color: var(--bg-black);
            color: white;
            font-family: var(--font-main);
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .grid-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: linear-gradient(rgba(0, 255, 159, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0, 255, 159, 0.03) 1px, transparent 1px);
            background-size: 30px 30px; z-index: -1;
        }

        .receipt-card {
            background: rgba(10, 10, 10, 0.95);
            border: 2px solid var(--neon-green);
            box-shadow: 0 0 20px rgba(0, 255, 159, 0.15);
            padding: 30px 20px;
            width: 100%; max-width: 450px;
            position: relative;
            /* Adjusted for mobile aspect ratios */
            clip-path: polygon(0 0, 100% 0, 100% 92%, 92% 100%, 0 100%);
        }

        .status-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .status-icon {
            font-size: 2.5rem;
            color: var(--neon-green);
            text-shadow: 0 0 15px var(--neon-green);
            margin-bottom: 5px;
        }

        h2 {
            font-family: var(--font-header);
            color: var(--neon-green);
            font-size: 1rem;
            letter-spacing: 2px;
            margin: 0;
        }

        .receipt-body {
            border-top: 1px dashed rgba(0, 255, 159, 0.2);
            border-bottom: 1px dashed rgba(0, 255, 159, 0.2);
            padding: 20px 0;
            margin: 20px 0;
        }

        .event-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            font-size: 0.75rem;
            color: #ccc;
            gap: 10px;
        }

        .event-name { 
            color: var(--neon-cyan); 
            line-height: 1.2;
            word-break: break-word;
        }

        .event-price {
            white-space: nowrap;
            font-weight: bold;
        }

        .total-section {
            display: flex;
            justify-content: space-between;
            font-family: var(--font-header);
            font-size: 1rem;
            color: var(--neon-green);
            padding-top: 5px;
        }

        .btn-print {
            width: 100%;
            background: transparent;
            border: 1px solid var(--neon-cyan);
            color: var(--neon-cyan);
            padding: 15px;
            font-family: var(--font-header);
            font-size: 0.8rem;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
            clip-path: polygon(8% 0, 100% 0, 100% 70%, 92% 100%, 0 100%, 0 30%);
            -webkit-tap-highlight-color: transparent;
        }

        .btn-print:active {
            background: var(--neon-cyan);
            color: black;
            transform: scale(0.98);
        }

        /* Print Media Queries */
        @media print {
            .btn-print, .grid-bg { display: none !important; }
            body { background: white !important; color: black !important; padding: 0; }
            .receipt-card { 
                border: 1px solid #000; 
                box-shadow: none; 
                clip-path: none; 
                max-width: 100%;
                color: black !important;
            }
            .event-name, .total-section, h2 { color: black !important; }
        }

        @media (min-width: 768px) {
            .receipt-card { padding: 40px; }
            h2 { font-size: 1.2rem; }
            .event-item { font-size: 0.85rem; }
            .total-section { font-size: 1.2rem; }
        }
    </style>
</head>
<body>

<div class="grid-bg"></div>

<div class="receipt-card">
    <div class="status-header">
        <div class="status-icon">✓</div>
        <h2>TRANSACTION_SUCCESSFUL</h2>
        <p style="font-size: 0.6rem; color: #666; margin-top: 10px;">
            AUTH_ID: <?php echo strtoupper(bin2hex(random_bytes(4))); ?>-PID<?php echo $participation_id; ?>
        </p>
    </div>

    <div class="receipt-body">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="event-item">
                <span class="event-name">> <?php echo strtoupper($row['event_name']); ?></span>
                <span class="event-price">₹<?php echo $row['price']; ?></span>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="total-section">
        <span>TOTAL_PAID</span>
        <span>₹<?php echo $total_price; ?></span>
    </div>

    <button class="btn-print" onclick="window.print()">GENERATE_HARD_COPY</button> <br>
    <br>
    <button class="btn-print" onclick="window.location.href='logout.php';" style="margin-top: 10px; border-color: var(--neon-pink); color: var(--neon-pink);">
       TERMINATE_SESSION_&_RETURN
    </button>
    
    
    <p style="text-align: center; font-size: 0.55rem; color: #555; margin-top: 25px; line-height: 1.5;">
        DIGITAL_SIGNATURE_VERIFIED<br>
        SECURE_LEDGER_CONFIRMED // GYANJYOTI_2026
    </p>
</div>

</body>
</html>