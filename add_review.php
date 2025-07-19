<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieId = $_POST['movie_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;
    $commentText = trim($_POST['comment'] ?? '');

    if (!$movieId || !$userId || empty($commentText)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO Comments (MovieID, UserID, CommentText) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $movieId, $userId, $commentText);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Review added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add review']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
