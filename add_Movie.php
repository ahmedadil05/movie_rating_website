<?php
session_start();
require 'php/db_connect.php'; // Ensure database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
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
    if (empty($movieName) || empty($genre) || empty($actors) || empty($director) || empty($duration) || empty($rating) || empty($trailerURL) || empty($movieURL) || empty($releaseDate) || empty($description)) {
        echo "All fields are required.";
        exit();
    }

    // Handle file uploads for poster and cover images
    $posterPath = '';
    $coverPath = '';

    if (isset($_FILES['movie_poster']) && $_FILES['movie_poster']['error'] === UPLOAD_ERR_OK) {
        $posterPath = 'uploads/' . basename($_FILES['movie_poster']['name']);
        move_uploaded_file($_FILES['movie_poster']['tmp_name'], $posterPath);
    }

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $coverPath = 'uploads/' . basename($_FILES['cover_image']['name']);
        move_uploaded_file($_FILES['cover_image']['tmp_name'], $coverPath);
    }

    // Insert movie into the database
    $stmt = $conn->prepare("INSERT INTO Movies (Title, Genre, Actors, Directors, Duration, Rating, TrailerURL, MovieURL, ReleaseDate, Description, poster_path, cover_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $movieName, $genre, $actors, $director, $duration, $rating, $trailerURL, $movieURL, $releaseDate, $description, $posterPath, $coverPath);

    if ($stmt->execute()) {
        echo "Movie added successfully!";
    } else {
        echo "Failed to add movie.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
