<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
// Database connection
require_once 'config/Database.php';
require_once 'config/config.php';


function calculatePercentage($percentage, $amount) {
    return ($percentage / 100) * $amount;
}

$couple_pay = COUPLE_PAY;
$single_pay = SINGLE_PAY;
$percentage = PERCENTAGE;
$charge = CHARGE;
$couple_pay_total = calculatePercentage($percentage,$couple_pay)+ $charge+ $couple_pay;
$single_pay_total = calculatePercentage($percentage,$single_pay)+ $charge + $single_pay;



// Get registration ID
$registration_id = $_GET['registration_id'];

// Fetch registration details
$sql = "SELECT * FROM registrations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $registration_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $registration_type = $row['registration_type'];
    $price = ($registration_type == 'single') ? $single_pay_total : $couple_pay_total; // Price in kobo
    $email = $row['email'];
} else {
    die("Registration not found.");
}
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$callback_url = PAYSTACK_CALLBACK."?registration_id=$registration_id";
// $callback_url =" https://a5c3-102-89-22-135.ngrok-free.app /event_register/verify_payment.php?registration_id=$registration_id";
// $callback_url = 
// Close connection
$stmt->close();
$conn->close();

// Initialize Paystack transaction
$url = PAYSTACK_URL;

$fields = [
    'email' => $email, // Use the dynamic email from the database
    'amount' => $price, // Use the dynamic price from the database
    'callback_url' =>$callback_url, // Redirect after payment
    'metadata' => [
        'registration_id' => $registration_id, // Pass registration ID for verification
    ]
];

$fields_string = json_encode($fields);

// Initialize cURL
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
   "Authorization: Bearer ".PAYSTACK_SECRET, // Replace with your Paystack secret key
   "Content-Type:".CONTENT_TYPE,
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request
$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    die("cURL Error: " . $err);
}

$result = json_decode($response, true);

if ($result['status'] === true) {
    // Redirect to Paystack payment page
    $authorization_url = $result['data']['authorization_url'];
    header("Location: $authorization_url");
} else {
    die("Paystack Error: " . $result['message']);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Payment Page</h1>
        <p class="text-gray-700 mb-4">Please complete your payment to confirm your registration.</p>
        <p class="text-gray-700"><strong>Registration Type:</strong> <?php echo ucfirst($registration_type); ?></p>
        <p class="text-gray-700 mb-6"><strong>Amount to Pay:</strong> ₦<?php echo number_format($price / 100, 2); ?></p>

        <button onclick="payWithPaystack()"
            class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Pay Now
        </button>

        <script>
        function payWithPaystack() {
            const amount = <?php echo $price; ?>; // Amount in kobo
            const email = "<?php echo $email; ?>";
            const registrationId = "<?php echo $registration_id; ?>";

            const handler = PaystackPop.setup({
                key: 'pk_test_9c060e8820802645281805b1ede06661dd61a66a', // Replace with your Paystack public key
                email: email,
                amount: amount,
                currency: 'NGN',
                ref: 'EVENT_REG_' + registrationId + '_' + Date.now(), // Unique reference
                callback: function(response) {
                    // Redirect to verify payment
                    window.location.href =
                        `verify_payment.php?reference=${response.reference}&registration_id=${registrationId}`;
                },
                onClose: function() {
                    alert('Payment window closed.');
                }
            });
            handler.openIframe();
        }
        </script>
    </div>
</body>

</html>