<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';
require __DIR__.'/config/db.php';
require __DIR__.'/config/key.php';

use Razorpay\Api\Api;

if (!isset($_SESSION['remail'])) {
    header("Location: ../index.php");
    exit();
}

$conn = getDB();
$email = $_SESSION['remail'];
$name=$_SESSION['rname'];
$_SESSION["email"]=$email;
$_SESSION['name']=$name;

$total_price = 0;
$event_id = [];
$category_id = [];


 //  GET PARTICIPATION ID

$stmt = $conn->prepare("SELECT participation_id FROM registrations WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo "Participant not found.";
    exit();
}

$row = $res->fetch_assoc();
$participation_id = $row['participation_id'];


 //GET ALREADY REGISTERED EVENTS
$existingEvents = [];
$stmt = $conn->prepare("SELECT event_id FROM event_registrations WHERE participation_id=?");
$stmt->bind_param("i", $participation_id);
$stmt->execute();
$result = $stmt->get_result();

while ($r = $result->fetch_assoc()) {
    $existingEvents[] = $r['event_id'];
}


  // PROCESS NEW SELECTION
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $technical = $_POST['technical'] ?? [];
    $cultural = $_POST['cultural'] ?? [];
    $fine_arts = $_POST['fine_arts'] ?? [];
    $fun_games = $_POST['fun_games'] ?? [];

    if (empty($technical) && empty($cultural) && empty($fine_arts) && empty($fun_games)) {
        echo "No data submitted.";
        exit;
    }

    $categories = [
        'technical' => $technical,
        'cultural' => $cultural,
        'fine_arts' => $fine_arts,
        'fun_games' => $fun_games
    ];

    foreach ($categories as $list) {
        if (!empty($list)) {

            $placeholders = implode(',', array_fill(0, count($list), '?'));
            $stmt = $conn->prepare("SELECT event_id, price, category_id FROM events WHERE event_name IN ($placeholders)");
            $types = str_repeat('s', count($list));
            $stmt->bind_param($types, ...$list);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {

                // Prevent duplicate event registration
                if (in_array($row['event_id'], $existingEvents)) {
                    continue;
                }

                $total_price += $row['price'];
                $event_id[] = $row['event_id'];
                $category_id[] = $row['category_id'];
            }
        }
    }

} else {
    echo "Invalid request.";
    exit;
}


if (empty($event_id)) {
    echo "No new events selected.";
    exit;
}

// in php session controls flow, cookies and security
$_SESSION['event_id'] = $event_id;
$_SESSION['category_id'] = $category_id;
$_SESSION['total_price'] = $total_price;
$_SESSION['advanced'] = true;


$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
$total = $total_price * 100;

$orderData = [
    'receipt' => uniqid(),
    'amount' => $total,
    'currency' => 'INR',
    'payment_capture' => 1
];

$order = $api->order->create($orderData);
$_SESSION['razorpay_order_id'] = $order['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADVANCED_SECURE_CHECKOUT</title>
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
            margin: 0; padding: 20px;
            background-color: var(--bg-black);
            color: white;
            font-family: var(--font-main);
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Background Effects */
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

        .checkout-container {
            background: rgba(10, 10, 10, 0.95);
            border: 2px solid var(--neon-pink);
            box-shadow: 0 0 25px rgba(255, 0, 255, 0.2);
            padding: 40px 30px;
            width: 100%; max-width: 420px;
            position: relative;
            clip-path: polygon(0 0, 92% 0, 100% 10%, 100% 100%, 8% 100%, 0 90%);
        }

        .checkout-container::before {
            content: "ADVANCED_ACCESS_ENCRYPTED";
            position: absolute; top: -12px; left: 20px;
            background: var(--bg-black);
            padding: 0 10px; font-size: 0.6rem; color: var(--neon-pink);
            letter-spacing: 2px;
        }

        h2 {
            font-family: var(--font-header);
            color: var(--neon-pink);
            text-align: center;
            letter-spacing: 2px;
            margin-top: 0;
            font-size: 1.1rem;
            text-shadow: 0 0 10px var(--neon-pink);
        }

        .summary-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(0, 243, 255, 0.1);
            padding: 20px;
            margin: 25px 0;
        }

        .row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 12px; 
            font-size: 0.8rem;
        }
        
        .row span:first-child { color: var(--neon-cyan); font-weight: bold; }
        
        .total-row {
            border-top: 1px dashed rgba(255, 0, 255, 0.3);
            padding-top: 15px; margin-top: 15px;
            font-size: 1.1rem;
            color: var(--neon-pink);
            font-family: var(--font-header);
        }

        #rzp-button {
            width: 100%;
            background: var(--neon-cyan);
            color: black;
            border: none;
            padding: 18px;
            font-family: var(--font-header);
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            clip-path: polygon(8% 0, 100% 0, 100% 70%, 92% 100%, 0 100%, 0 30%);
            box-shadow: 0 0 15px var(--neon-cyan);
            -webkit-tap-highlight-color: transparent;
        }

        #rzp-button:hover {
            background: white;
            box-shadow: 0 0 30px white;
            transform: translateY(-2px);
        }

        .footer-note {
            font-size: 0.6rem;
            color: #444;
            text-align: center;
            margin-top: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 480px) {
            .checkout-container { padding: 30px 20px; }
            h2 { font-size: 0.95rem; }
        }
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="scanlines"></div>

<div class="checkout-container">
    <h2>ADVANCED_ORDER_VERIFICATION</h2>
    
    <div class="summary-card">
        <div class="row">
            <span>OPERATOR</span>
            <span><?php echo htmlspecialchars(explode(' ', $name)[0]); ?></span>
        </div>
        <div class="row">
            <span>TXN_ID</span>
            <span>#<?php echo substr($order['id'], -8); ?></span>
        </div>
        <div class="row">
            <span>NEW_MODULES</span>
            <span><?php echo count($event_id); ?> UNITS</span>
        </div>
        <div class="row total-row">
            <span>PAYABLE</span>
            <span>₹<?php echo $total_price; ?></span>
        </div>
    </div>

    <button id="rzp-button">AUTHORIZE_PAYMENT</button>
    
    <p class="footer-note">Secure Protocol 2.4.0 // GyanJyoti 2026</p>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?php echo RAZORPAY_KEY_ID; ?>",
    "amount": "<?php echo $total; ?>",
    "currency": "INR",
    "name": "GyanJyoti 2026",
    "description": "Advanced Event Registration",
    "order_id": "<?php echo $order['id']; ?>",
    "handler": function (response) {
        window.location.href = "verify_payment.php?payment_id="
            + response.razorpay_payment_id
            + "&order_id="
            + response.razorpay_order_id
            + "&signature="
            + response.razorpay_signature;
    }
};

var rzp1 = new Razorpay(options);

document.getElementById('rzp-button').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script>

</body>
</html>