<?php 
session_start();
require 'db_connect.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely retrieve form data
    $email_or_username = $_POST['login_email'] ?? null;
    $password = $_POST['login_password'] ?? null;

    // Validate inputs
    if (!$email_or_username || !$password) {
        die("Error: Both fields are required.");
    }

    // Check if the user exists using email or username
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ? OR Email = ?");
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['Password'])) {
            // Store user info in the session
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['Role']; // Add the role to the session

            // Redirect to the homepage
            header("Location: ../index.php");
            exit();
        } else {
            echo "Error: Invalid username/email or password.";
        }
    } else {
        echo "Error: No user found with the provided credentials.";
    }

    // Close database resources
    $stmt->close();
    $conn->close();
} else {
    // Reject requests that are not POST
    echo "Invalid request method.";
}
?>

