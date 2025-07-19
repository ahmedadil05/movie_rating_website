<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewId = $_POST['review_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;
    $userRole = $_SESSION['role'] ?? null;

    if (!$reviewId || !$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        exit();
    }

    // Check if the user is authorized to delete the review
    $stmt = $conn->prepare("SELECT UserID FROM Comments WHERE CommentID = ?");
    $stmt->bind_param("i", $reviewId);
    $stmt->execute();
    $result = $stmt->get_result();
    $review = $result->fetch_assoc();
    $stmt->close();

    if (!$review || ($review['UserID'] != $userId && $userRole != 'Admin')) {
        echo json_encode(['status' => 'error', 'message' => 'You are not authorized to delete this comment.']);
        exit();
    }

    // Delete the review
    $deleteStmt = $conn->prepare("DELETE FROM Comments WHERE CommentID = ?");
    $deleteStmt->bind_param("i", $reviewId);
    if ($deleteStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Comment deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete comment.']);
    }
    $deleteStmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
