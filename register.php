<?php
session_start();
require 'db_connect.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely retrieve form data
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    // Validate inputs
    if (!$username || !$email || !$password) {
        die("Error: All fields are required.");
    }

    // Check if email or username already exists in the database
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ? OR Email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email or username already in use
        echo "Error: Username or email already in use.";
    } else {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO Users (Username, Email, Password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            // Store user information in the session
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $username;

            // Redirect to the homepage
            header("Location: ../index.php");
            exit();
        } else {
            // Handle database errors
            echo "Error: Unable to register. Please try again.";
        }
    }

    // Close database resources
    $stmt->close();
    $conn->close();
} else {
    // Reject requests that are not POST
    echo "Invalid request method.";
}
?>
