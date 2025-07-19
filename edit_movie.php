<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db_connect.php'; // Ensure database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieId = $_POST['movie_id'] ?? null;
    $movieName = $_POST['movie_name'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $actors = $_POST['actors'] ?? '';
    $director = $_POST['director'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $trailerURL = $_POST['trailerurl'] ?? '';
    $movieURL = $_POST['movieurl'] ?? '';
    $releaseDate = $_POST['realasedate'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validate required fields
    if (empty($movieId) || !is_numeric($movieId) || empty($movieName) || empty($genre) || empty($actors) || empty($director) || empty($duration) || empty($rating) || empty($trailerURL) || empty($movieURL) || empty($releaseDate) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    // Initialize file paths
    $posterPath = '';
    $coverPath = '';
    $uploadDir = '../uploads/';

    // Handle poster image upload
    if (isset($_FILES['movie_poster']) && $_FILES['movie_poster']['error'] === UPLOAD_ERR_OK) {
        $posterFileName = basename($_FILES['movie_poster']['name']);
        $posterPath = $uploadDir . $posterFileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES['movie_poster']['tmp_name'], $posterPath)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload poster image.']);
            exit();
        }

        // Remove '../' prefix for saving in the database
        $posterPath = 'uploads/' . $posterFileName;
    }

    // Handle cover image upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $coverFileName = basename($_FILES['cover_image']['name']);
        $coverPath = $uploadDir . $coverFileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $coverPath)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload cover image.']);
            exit();
        }

        // Remove '../' prefix for saving in the database
        $coverPath = 'uploads/' . $coverFileName;
    }

    // Fetch existing file paths if new images are not uploaded
    if (empty($posterPath) || empty($coverPath)) {
        $stmt = $conn->prepare("SELECT poster_path, cover_path FROM Movies WHERE MovieID = ?");
        $stmt->bind_param("i", $movieId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (empty($posterPath)) {
                $posterPath = $row['poster_path'];
            }
            if (empty($coverPath)) {
                $coverPath = $row['cover_path'];
            }
        }
        $stmt->close();
    }

    // Update the database with the new details
    $stmt = $conn->prepare(
        "UPDATE Movies 
        SET Title = ?, Genre = ?, Actors = ?, Directors = ?, Duration = ?, Rating = ?, TrailerURL = ?, MovieURL = ?, ReleaseDate = ?, Description = ?, poster_path = ?, cover_path = ? 
        WHERE MovieID = ?"
    );

    $stmt->bind_param(
        "ssssssssssssi",
        $movieName, $genre, $actors, $director, $duration, $rating, $trailerURL, $movieURL, $releaseDate, $description, $posterPath, $coverPath, $movieId
    );

    // Execute the query and check for errors
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Movie updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update movie: ' . $stmt->error]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
