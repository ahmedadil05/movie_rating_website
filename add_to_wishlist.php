<?php
session_start();
require 'db_connect.php'; // Ensure this connects to your database properly.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to update your wishlist.']);
        exit();
    }

    $userId = $_SESSION['user_id'];
    $movieId = isset($_POST['movie_id']) ? intval($_POST['movie_id']) : null;
    $action = $_POST['action'] ?? 'add';

    if (!$movieId) {
        echo json_encode(['status' => 'error', 'message' => 'Movie ID is required.']);
        exit();
    }

    try {
        if ($action === 'add') {
            // Check if the movie is already in the wishlist
            $stmt = $conn->prepare("SELECT 1 FROM WatchList WHERE UserID = ? AND MovieID = ?");
            $stmt->bind_param("ii", $userId, $movieId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Movie is already in your wishlist.']);
            } else {
                // Add the movie to the wishlist
                $stmt = $conn->prepare("INSERT INTO WatchList (UserID, MovieID) VALUES (?, ?)");
                $stmt->bind_param("ii", $userId, $movieId);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Movie added to your wishlist.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to add movie to wishlist.']);
                }
            }
        } elseif ($action === 'remove') {
            // Remove the movie from the wishlist
            $stmt = $conn->prepare("DELETE FROM WatchList WHERE UserID = ? AND MovieID = ?");
            $stmt->bind_param("ii", $userId, $movieId);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Movie removed from your wishlist.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to remove movie from wishlist.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
    }

    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}
