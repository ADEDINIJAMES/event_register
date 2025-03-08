<?php
session_start();
require_once 'config/Database.php';
require_once 'config/config.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $attendance = $_POST['attendance'];

    // Validate input
    if (!is_numeric($attendance) || $attendance < 0) {
        die("Invalid attendance value.");
    }

    // Update the attendance in the database
    $stmt = $conn->prepare("UPDATE registrations SET attendance = ? WHERE id = ?");
    $stmt->bind_param("ii", $attendance, $id);

    if ($stmt->execute()) {
        // Redirect back to the main page after updating
        header('Location: print.php');
        exit;
    } else {
        die("Error updating attendance: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>