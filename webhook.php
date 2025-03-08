<?php

require_once 'config/Database.php';
require_once 'config/config.php';
// Retrieve the request's body
$input = @file_get_contents("php://input");
$event = json_decode($input);

// Verify the event signature
$secret = PAYSTACK_SECRET; // Replace with your Paystack secret key
$signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_hmac('sha512', $input, $secret) === $signature) {
    // Process the event
    switch ($event->event) {
        case 'charge.success':
            // Handle successful payment
            $reference = $event->data->reference;
            // Update your database or trigger other actions
            break;
        case 'refund.processed':
            // Handle refund
            break;
        default:
            // Handle other events
            break;
    }
    http_response_code(200);
} else {
    http_response_code(400);
}
?>