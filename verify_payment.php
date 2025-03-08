<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'config/Database.php';
require_once 'config/config.php';
// Include PHPMailer manually
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);


// Get Paystack reference and registration ID
$reference = $_GET['reference'];
$registration_id = $_GET['registration_id'];

// Verify payment with Paystack API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => PAYSTACK_CURLOPT_URL."$reference",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization:Bearer ".PAYSTACK_SECRET, // Replace with your Paystack secret key
        "Cache-Control: no-cache"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("cURL Error: " . $err);
}

// file_put_contents('paystack_response.txt', $response); // Log the response

$result = json_decode($response, true);

if (!$result) {
    die("Invalid Paystack API response.");
}

if ($result['data']['status'] === 'success') {
    // Payment successful
    $amount = $result['data']['amount'] / 100; // Convert from kobo to Naira
    $paystack_ref = $result['data']['reference'];

    // Generate a unique ticket number
    function generateTicketNo() {
        $year= date("y");
        $month = date("n");
        $day = date("j");
        $prefix = "TGCOM-";
        $timestamp = $year.$month.$day; // Current timestamp
        $random = mt_rand(10000, 99999); // Random 5-digit number
        return $prefix . $timestamp . "-" . $random;
    }

    $ticket_no = generateTicketNo();
     $sql = "SELECT name, name2, email FROM registrations WHERE id = $registration_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
      $name2 = !empty($row['name2']) ? $row['name2'] : "No Partner";
        $email = $row['email'];
    } else {
        die("Registration not found.");
    }


    // Update database with payment status, Paystack reference, and ticket number
    $sql = "UPDATE registrations SET payment_status='completed', paystack_ref='$paystack_ref', ticket='$ticket_no' WHERE id=$registration_id";
    // file_put_contents('database_query.txt', $sql); // Log the query

      if ($conn->query($sql) === TRUE) {
       
        // // Send email to the user
        // $to = $email;
        // $subject = "Your Event Ticket";
        // $message = "Dear $name,\n\n";
        // $message .= "Thank you for registering for the event. Below are your ticket details:\n\n";
        // $message .= "Ticket Number: $ticket_no\n";
        // $message .= "Event: Your Event Name\n";
        // $message .= "Amount Paid: ₦" . number_format($amount, 2) . "\n\n";
        // $message .= "We look forward to seeing you at the event!\n\n";
        // $message .= "Best regards,\nEvent Organizers";

        // $headers = "From: myvendingmachineajo@gmail.com"; // Replace with your email

        // if (mail($to, $subject, $message, $headers)) {
        //     // Redirect to ticket generation page
        //     header("Location: generate_ticket.php?registration_id=$registration_id");
        // } else {
        //     die("Failed to send email. Check server logs for details.");
        // }






try {
 // Server settings
    $mail->isSMTP();
    $mail->Host = EMAIL_HOST; // Gmail SMTP server
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = EMAIL_USERNAME; // Your Gmail address
    $mail->Password = EMAIL_PASSWORD; // Your Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port = EMAIL_PORT; // TCP port to connect to

    // Recipients
    $mail->setFrom(EMAIL_USERNAME, EMAIL_NAME); // Sender
    $mail->addAddress($email, $name); // Recipient

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Ticket'; // Email subject

    // Email body (HTML and plain text)
    $message = "Dear $name,<br><br>";
    $message .= "Thank you for registering for the event. Below are your ticket details:<br><br>";
    $message .= "<strong>Ticket Number:</strong> $ticket_no<br>";
    $message .= "<strong>Partner:</strong> $name2<br>";
    $message .= "<strong>Event:</strong> Great Commission Ministries - The lost Mandate Premiere<br>";
    $message .= "<strong>Amount Paid:</strong> ₦" . number_format($amount, 2) . "<br><br>";
    $message .= "We look forward to seeing you at the event!<br><br>";
    $message .= "Best regards,<br> Organizers";

    // Plain text version for non-HTML email clients
    $plainMessage = "Dear $name,\n\n";
    $plainMessage .= "Thank you for registering for the event. Below are your ticket details:\n\n";
    $plainMessage .= "Ticket Number: $ticket_no\n";
    $plainMessage .= "Partner: $name2\n";
    $plainMessage .= "Event: Great Commission Ministries - The Lost Mandate Premiere\n";
    $plainMessage .= "Amount Paid: ₦" . number_format($amount, 2) . "\n\n";
    $plainMessage .= "We look forward to seeing you at the event!\n\n";
    $plainMessage .= "Best regards,\n Organizers";

    $mail->Body = $message; // HTML version
    $mail->AltBody = $plainMessage; // Plain text version

    // Send email
    $mail->send();
    echo 'Email sent successfully.';
    header("Location: generate_ticket.php?registration_id=$registration_id");
} catch (Exception $e) {
    echo "Failed to send email. Error: {$mail->ErrorInfo}";
}



    } else {
        error_log("Database Error: " . $conn->error);
        die("An error occurred. Please try again later.");
    }
} else {
    echo "Payment verification failed.";
}

// Close connection
$conn->close();
?>