<?php
require 'db_connect.php'; // Ensure this connects to your database

$selectedGenres = $_GET['genres'] ?? [];
$searchQuery = $_GET['query'] ?? '';

// Build SQL Query
if (!empty($selectedGenres) && $searchQuery) {
    $placeholders = implode(',', array_fill(0, count($selectedGenres), '?'));
    $sql = "SELECT * FROM Movies WHERE Title LIKE ? AND Genre IN ($placeholders) ORDER BY Title ASC";
    $params = array_merge(["%" . $searchQuery . "%"], $selectedGenres);
} elseif (!empty($selectedGenres)) {
    $placeholders = implode(',', array_fill(0, count($selectedGenres), '?'));
    $sql = "SELECT * FROM Movies WHERE Genre IN ($placeholders) ORDER BY Title ASC";
    $params = $selectedGenres;
} elseif ($searchQuery) {
    $sql = "SELECT * FROM Movies WHERE Title LIKE ? ORDER BY Title ASC";
    $params = ["%" . $searchQuery . "%"];
} else {
    $sql = "SELECT * FROM Movies ORDER BY Title ASC";
    $params = [];
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$movies = [];
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}
echo json_encode($movies);
?>
