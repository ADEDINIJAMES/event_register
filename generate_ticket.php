<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'config/Database.php';
require_once 'config/config.php';


// Get registration ID
if (!isset($_GET['registration_id'])) {
    die("Registration ID not provided.");
}

$registration_id = intval($_GET['registration_id']); // Sanitize the input

// Fetch registration details
$sql = "SELECT name, email, event, name2, registration_type, ticket FROM registrations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $registration_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $name2 =!empty($row['name2'])?$row['name2']: "no Partner";
    $email = $row['email'];
    $registration_type = $row['registration_type'];
    $event = $row['event'];
    $ticket_no = $row['ticket']; // Ensure the column name is correct
} else {
    die("Registration not found.");
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Generated</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Ticket Generated Successfully</h1>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <p class="text-gray-700"><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p class="text-gray-700"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p class="text-gray-700"><strong>Event:</strong> <?php echo htmlspecialchars($event); ?></p>
            <p class="text-gray-700"><strong>Ticket Number:</strong> <?php echo htmlspecialchars($ticket_no); ?></p>
            <p class="text-gray-700"><strong>Partner:</strong> <?php echo htmlspecialchars($name2); ?></p>
            <p class="text-gray-700"><strong>Registration Type:</strong>
                <?php echo htmlspecialchars($registration_type); ?>
            </p>

            </p>

        </div>
        <p class="text-gray-700 text-center">
            <b>Please copy your ticket number, as it will be required for entry. A copy of your ticket has been sent to
                your email. </b>
            <i> Please contact for help : 08102745651 , 08034370707, 081663703678</i>




        </p>

        <button> <a href="index.php" class="bg-green-500 text-white p-2 rounded inline-block mt-4">Register</a></button>

    </div>
</body>

</html>