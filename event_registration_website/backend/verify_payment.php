<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/config/key.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

$attributes = [
    'razorpay_order_id' => $_GET['order_id'],
    'razorpay_payment_id' => $_GET['payment_id'],
    'razorpay_signature' => $_GET['signature']
];

try {
    $api->utility->verifyPaymentSignature($attributes);

    echo "Payment Successful";

    header("Location:register_qr_and_events.php");
    exit();

} catch(SignatureVerificationError $e) {
    echo "Payment Verification Failed";
}
?>