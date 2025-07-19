<?php
session_start();
require 'db_connect.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access.'
    ]);
    http_response_code(401); // Unauthorized
    exit();
}

$userId = $_SESSION['user_id'];
$newUsername = trim($_POST['username'] ?? '');
$newEmail = trim($_POST['email'] ?? '');

// Fetch current user details
$stmt = $conn->prepare("SELECT Username, Email FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'User not found.'
    ]);
    http_response_code(404); // Not Found
    exit();
}

$currentUser = $result->fetch_assoc();
$currentUsername = $currentUser['Username'];
$currentEmail = $currentUser['Email'];

// Initialize flags for updates
$usernameChanged = false;
$emailChanged = false;

// Validate and process new username
if (!empty($newUsername) && $newUsername !== $currentUsername) {
    if (strlen($newUsername) < 3 || strlen($newUsername) > 20) {
        echo json_encode([
            'success' => false,
            'message' => 'The username must be between 3 and 20 characters long.'
        ]);
        http_response_code(400); // Bad Request
        exit();
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $newUsername)) {
        echo json_encode([
            'success' => false,
            'message' => 'The username can only contain letters, numbers, and underscores.'
        ]);
        http_response_code(400); // Bad Request
        exit();
    }

    // Check if username is already in use
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE Username = ? AND UserID != ?");
    $stmt->bind_param("si", $newUsername, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'The username is already in use by another account.'
        ]);
        http_response_code(400); // Bad Request
        exit();
    }

    $usernameChanged = true;
}

// Validate and process new email
if (!empty($newEmail) && $newEmail !== $currentEmail) {
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'The email address is not valid.'
        ]);
        http_response_code(400); // Bad Request
        exit();
    }

    // Check if email is already in use
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE Email = ? AND UserID != ?");
    $stmt->bind_param("si", $newEmail, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'The email address is already in use by another account.'
        ]);
        http_response_code(400); // Bad Request
        exit();
    }

    $emailChanged = true;
}

// Update only if changes are made
try {
    if ($usernameChanged) {
        $stmt = $conn->prepare("UPDATE Users SET Username = ? WHERE UserID = ?");
        $stmt->bind_param("si", $newUsername, $userId);
        $stmt->execute();
        $_SESSION['username'] = $newUsername; // Update session
    }

    if ($emailChanged) {
        $stmt = $conn->prepare("UPDATE Users SET Email = ? WHERE UserID = ?");
        $stmt->bind_param("si", $newEmail, $userId);
        $stmt->execute();
    }

    if ($usernameChanged || $emailChanged) {
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No changes detected. Please modify the username or email.'
        ]);
        http_response_code(400); // Bad Request
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred: ' . $e->getMessage()
    ]);
    http_response_code(500); // Internal Server Error
}

$stmt->close();
$conn->close();
?>
