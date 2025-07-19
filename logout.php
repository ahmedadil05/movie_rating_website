<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Destroy the session to log out the user
    session_destroy();
    // Redirect to the homepage
    header("Location: ../login.html");
    exit();
} else {
    // Redirect to the login page (HTML interface) if no user is logged in
    header("Location: ../login.html");
    exit();
}
?>
