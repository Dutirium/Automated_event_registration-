<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';
require __DIR__.'/config/db.php';
require __DIR__.'/config/key.php';

$category_id=[];
$total_price=0;
$event_id=[];
$conn=getDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $technical      = $_POST['technical'] ?? [];
    $cultural       = $_POST['cultural'] ?? [];
    $fine_arts      = $_POST['fine_arts'] ?? [];
    $fun_and_games  = $_POST['fun_games'] ?? [];

    if (
        empty($technical) &&
        empty($cultural) &&
        empty($fine_arts) &&
        empty($fun_and_games)
    ) {
        echo "No data submitted.";
        exit;
    }

    // Array of all categories to process
    $categories = [
        'technical' => $technical,
        'cultural' => $cultural,
        'fine_arts' => $fine_arts,
        'fun_games' => $fun_and_games
    ];

    foreach ($categories as $list) {
        if (!empty($list)) {
            $placeholders = implode(',', array_fill(0, count($list), '?'));
            $stmt = $conn->prepare("SELECT event_id, price, category_id FROM events WHERE event_name IN ($placeholders)");
            $types = str_repeat('s', count($list));
            $stmt->bind_param($types, ...$list);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_price += $row['price'];
                    $event_id[] = $row['event_id'];
                    $category_id[] = $row['category_id'];
                }
            }
        }
    }

} else {
    echo "Invalid request.";
    exit;
}

$_SESSION['event_id']=$event_id;
$_SESSION['category_id']=$category_id;
$_SESSION['total_price']=$total_price;

use Razorpay\Api\Api;
$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
$total = $total_price * 100;

$orderData = [
    'receipt'         => uniqid(),
    'amount'          => $total,
    'currency'        => 'INR',
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
    <title>SECURE_CHECKOUT</title>
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
            min-height: 100vh; /* Changed from height to min-height for mobile */
            overflow-x: hidden;
        }

        .grid-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: linear-gradient(rgba(0, 243, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0, 243, 255, 0.03) 1px, transparent 1px);
            background-size: 30px 30px; z-index: -1;
        }

        .checkout-container {
            background: rgba(10, 10, 10, 0.95);
            border: 2px solid var(--neon-pink);
            box-shadow: 0 0 20px rgba(255, 0, 255, 0.15);
            padding: 30px 20px;
            width: 100%; max-width: 420px;
            position: relative;
            clip-path: polygon(0 0, 90% 0, 100% 8%, 100% 100%, 10% 100%, 0 92%);
        }

        h2 {
            font-family: var(--font-header);
            color: var(--neon-pink);
            text-align: center;
            letter-spacing: 2px;
            margin-top: 0;
            font-size: 1.2rem;
            text-shadow: 0 0 8px var(--neon-pink);
        }

        .summary-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(0, 243, 255, 0.1);
            padding: 20px 15px;
            margin: 20px 0;
        }

        .row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 15px; 
            font-size: 0.8rem; 
            gap: 10px;
        }
        
        .row span:first-child { color: var(--neon-cyan); font-weight: bold; }
        .row span:last-child { text-align: right; word-break: break-all; }
        
        .total-row {
            border-top: 1px solid rgba(255, 0, 255, 0.3);
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
            padding: 16px;
            font-family: var(--font-header);
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            clip-path: polygon(10% 0, 100% 0, 100% 70%, 90% 100%, 0 100%, 0 30%);
            box-shadow: 0 0 12px var(--neon-cyan);
            -webkit-tap-highlight-color: transparent;
        }

        #rzp-button:active {
            transform: scale(0.98);
            filter: brightness(1.2);
        }

        .footer-note {
            font-size: 0.6rem;
            color: #666;
            text-align: center;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (min-width: 768px) {
            h2 { font-size: 1.5rem; }
            .row { font-size: 0.9rem; }
            .checkout-container { padding: 40px; }
        }
    </style>
</head>
<body>

<div class="grid-bg"></div>

<div class="checkout-container">
    <h2>ORDER_VERIFICATION</h2>
    
    <div class="summary-card">
        <div class="row">
            <span>TXN_ID</span>
            <span>#<?php echo substr($order['id'], -10); ?></span>
        </div>
        <div class="row">
            <span>MODULES</span>
            <span><?php echo count($event_id); ?> UNITS</span>
        </div>
        <div class="row total-row">
            <span>PAYABLE</span>
            <span>₹<?php echo $total_price; ?></span>
        </div>
    </div>

    <button id="rzp-button">AUTHORIZE_PAYMENT</button>
    
    <p class="footer-note">Secure Protocol // Port 443 Active</p>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?php echo RAZORPAY_KEY_ID; ?>",
    "amount": "<?php echo $total; ?>",
    "currency": "INR",
    "name": "GyanJyoti 2026",
    "description": "Event Registration",
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