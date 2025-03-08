<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/Database.php';
// // Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "eventReg";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Get form data
$name = $_POST['name'];
$name2 = isset($_POST['name2']) ? $_POST['name2'] : null; // Second person's name (for couples)
$email = $_POST['email'];
$phone = $_POST['phone'];
$event = $_POST['event'];
$registration_type = $_POST['registration_type'];

// Insert data into database
$sql = "INSERT INTO registrations (name, name2, email, phone, event, registration_type, payment_status) 
        VALUES ('$name', '$name2', '$email', '$phone', '$event', '$registration_type', 'pending')";

if ($conn->query($sql) === TRUE) {
    $registration_id = $conn->insert_id; // Get the last inserted ID
    header("Location: payment.php?registration_id=" . $registration_id); // Redirect to payment page
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>