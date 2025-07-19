<?php
require 'db_connect.php';

$movieId = $_GET['id'] ?? null;

if (!$movieId || !is_numeric($movieId)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid movie ID']);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM Movies WHERE MovieID = ?");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $movie = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'data' => $movie]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Movie not found']);
}

$stmt->close();
$conn->close();
?>
