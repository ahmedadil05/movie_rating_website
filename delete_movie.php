<?php
session_start();
require 'db_connect.php';

// Function to show an alert and stop execution
function show_alert_and_exit($message) {
    echo "<script>alert('$message'); window.location.href = '../index.php';</script>";
    exit();
}

// Check admin role
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    show_alert_and_exit("Unauthorized access.");
}

$movieId = $_POST['id'] ?? null;

if (!$movieId || !is_numeric($movieId)) {
    show_alert_and_exit("Invalid movie ID.");
}

// Prepare and execute the deletion
$stmt = $conn->prepare("DELETE FROM Movies WHERE MovieID = ?");
$stmt->bind_param("i", $movieId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        show_alert_and_exit("Movie deleted successfully.");
    } else {
        show_alert_and_exit("Movie not found.");
    }
} else {
    show_alert_and_exit("Failed to delete movie.");
}

$stmt->close();
$conn->close();
?>
