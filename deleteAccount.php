<?php
session_start();
require 'db_connect.php';
ob_start(); // Start output buffering

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Unauthorized access.";
    header("Location: ../settings.php");
    exit();
}

$userId = $_SESSION['user_id'];
$password = $_POST['password'] ?? '';

// Validate the password
if (empty($password)) {
    $_SESSION['error_message'] = "Password is required to delete the account.";
    header("Location: ../settings.php");
    exit();
}

// Fetch the current hashed password
$stmt = $conn->prepare("SELECT Password FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "User not found.";
    header("Location: ../settings.php");
    exit();
}

$user = $result->fetch_assoc();
if (!password_verify($password, $user['Password'])) {
    $_SESSION['error_message'] = "Password is incorrect.";
    header("Location: ../settings.php");
    exit();
}

// Delete related data
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("DELETE FROM WatchList WHERE UserID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM Comments WHERE UserID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $conn->commit();

    $_SESSION = [];
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');

    header("Location: ../index.php");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error deleting account: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred while deleting your account.";
    header("Location: ../settings.php");
    exit();
}

$stmt->close();
$conn->close();
?>
