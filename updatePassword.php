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
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';

// Validate inputs
if (empty($currentPassword) || empty($newPassword)) {
    echo json_encode([
        'success' => false,
        'message' => 'Both current and new passwords are required.'
    ]);
    http_response_code(400); // Bad Request
    exit();
}

// Fetch current password hash from the database
try {
    $stmt = $conn->prepare("SELECT Password FROM Users WHERE UserID = ?");
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

    $user = $result->fetch_assoc();

    // Verify current password
    if (!password_verify($currentPassword, $user['Password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Current password is incorrect.'
        ]);
        http_response_code(401); // Unauthorized
        exit();
    }

    // Check if the new password is the same as the current password
    if (password_verify($newPassword, $user['Password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'The new password cannot be the same as the current password.'
        ]);
        http_response_code(400); // Bad Request
        exit();
    }

    // Validate new password complexity (minimum 8 characters, at least one number, one uppercase letter)
    if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
        echo json_encode([
            'success' => false,
            'message' => 'The new password must be at least 8 characters long and include at least one number and one uppercase letter.'
        ]);
        http_response_code(400); // Bad Request
        exit();
    }

    // Hash new password and update the database
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE Users SET Password = ? WHERE UserID = ?");
    $stmt->bind_param("si", $newHashedPassword, $userId);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating password.'
        ]);
        http_response_code(500); // Internal Server Error
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
