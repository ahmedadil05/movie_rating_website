<?php
session_start();
require 'php/db_connect.php'; // Include the database connection file

// Restrict access to admins
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.html"); // Redirect non-admin users to login
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $errorMessage = "All fields are required.";
    } else {
        // Handle file uploads
        $posterPath = '';
        $coverPath = '';
        $uploadDir = 'uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create upload directory if it doesn't exist
        }

        // Sanitize movie name for safe file naming
        $safeMovieName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($movieName));

        // Handle movie poster upload
        if (isset($_FILES['movie_poster']) && $_FILES['movie_poster']['error'] === UPLOAD_ERR_OK) {
            $posterExtension = pathinfo($_FILES['movie_poster']['name'], PATHINFO_EXTENSION);
            $posterPath = $uploadDir . $safeMovieName . '_poster_' . uniqid() . '.' . $posterExtension;
            move_uploaded_file($_FILES['movie_poster']['tmp_name'], $posterPath);
        }

        // Handle cover image upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $coverExtension = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
            $coverPath = $uploadDir . $safeMovieName . '_background_' . uniqid() . '.' . $coverExtension;
            move_uploaded_file($_FILES['cover_image']['tmp_name'], $coverPath);
        }

        // Insert movie into the database
        $stmt = $conn->prepare(
            "INSERT INTO Movies (Title, Genre, Actors, Directors, Duration, Rating, TrailerURL, MovieURL, ReleaseDate, Description, poster_path, cover_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssssssssss", $movieName, $genre, $actors, $director, $duration, $rating, $trailerURL, $movieURL, $releaseDate, $description, $posterPath, $coverPath);

        if ($stmt->execute()) {
            $successMessage = "Movie added successfully!";
        } else {
            $errorMessage = "Failed to add movie: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add a Movie</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/addMovie.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/14f233c83a.js" crossorigin="anonymous"></script>
</head>
<body>  
    <div class="home">
        <!-- Sidebar -->
        <aside class="sidebar">
          <div class="logo">
            <i class="fa-solid fa-mug-hot logo-icon"></i>
            <h1>WATCH</h1>
          </div>
          <nav>
            <ul>
              <li><a href="index.php">Home</a></li>
              <li><a href="favourite.php">Favourites</a></li>
              <li><a href="category.php">Category</a></li>
              <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'): ?><li><a href="addmovie.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'addmovie.php' ? 'active' : ''; ?>">Add a Movie</a></li><?php endif; ?>

            </ul>
          </nav>
          <div class="bottom-menu">
            <ul>
            <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="settings.php" class="active">Settings</a></li>
        <?php endif; ?>
              <li><a href="php/logout.php"><?php echo isset($_SESSION['username']) ? 'Logout' : 'Login'; ?></a></li>
            </ul>
          </div>
        </aside>

      <!-- Main Form Container -->
      <form id="add-movie-form" class="container"  action="addmovie.php" method="post"  enctype="multipart/form-data">
        <!-- Form Section -->
        <div class="form-section">
          <div class="form-group">
            <p>Movie Name</p>
            <input type="text" name="movie_name" required id="movieName" />
          </div>
          <div class="form-group">
            <p>Genre</p>
            <select name="genre" required>
              <option value="">Select Genre</option>
              <option value="action">Action</option>
              <option value="adventure">Adventure</option>
              <option value="animation">Animation</option>
              <option value="comedy">Comedy</option>
              <option value="drama">Drama</option>
              <option value="horror">Horror</option>
              <option value="romance">Romance</option>
              <option value="thriller">Thriller</option>
              <option value="mystery">Mystery</option>
              <option value="crime">Crime</option>
              <option value="family">Family</option>
              <option value="sci-fi">Sci-Fi</option>
              <option value="fantasy">Fantasy</option>
              <option value="documentary">Documentary</option>
              <option value="music">Music</option>
              <option value="musical">Musical</option>
              <option value="western">Western</option>
              <option value="war">War</option>
              <option value="history">History</option>
            </select>
          </div>
          <div class="form-group">
            <p>Actors</p>
            <input type="text" name="actors" required />
          </div>
          <div class="form-group">
            <p>Director</p>
            <input type="text" name="director" required />
          </div>
          <div class="form-group">
            <p>Duration</p>
            <input type="number" name="duration" required />
          </div>
          <div class="form-group">
            <p>IMDB Rating</p>
            <input type="number" name="rating" required />
          </div>
          <div class="form-group">
            <p>Trailer URL</p>
            <input type="text" name="trailerurl" required />
          </div>
          <div class="form-group">
          <p>Movie URL</p>
          <input type="text" name="movieurl" required />
        </div>
        <div class="form-group">
        <p>Release Date</p>
        <input type="text" name="realasedate" required 
           pattern="\d{4}-\d{2}-\d{2}" 
           title="Date must be in the format YYYY-MM-DD" 
           placeholder="YYYY-MM-DD" 
           oninput="validateDateInput(this)" />
      </div>
          <div class="form-group">
            <p>Description</p>
            <textarea name="description" required></textarea>
          </div>      
        </div>

<!-- Media Section -->
<div class="media-section">
    <p>Movie Poster</p>
    <label class="image-button">
      <input type="file" name="movie_poster" accept="image/*" style="display: none;" required onchange="previewImage(event, 'poster-preview')" />
      <img id="poster-preview" src="assets/images/plus.png" alt="Add Poster" />
    </label>
    <br>
    <p>Cover Image</p>
    <label class="image-button">
      <input type="file" name="cover_image" accept="image/*" style="display: none;" required onchange="previewImage(event, 'cover-preview')" />
      <img id="cover-preview" src="assets/images/plus.png" alt="Add Cover" />
    </label>
  </div>

        <!-- Add Movie Button -->
        <button class="add-btn" type="submit" id="up">Add Movie</button>
        <div id="success-modal" class="modal hidden">
          <div class="modal-content">
              <h1 id="modal-message">Movie Successfully Added</h1>             
              <button id="confirm-yes" class="btn">Close</button>
          </div>
      </div>
      </form>

      <?php if (isset($successMessage)) echo "<div class='alert success'>$successMessage</div>"; ?>
      <?php if (isset($errorMessage)) echo "<div class='alert error'>$errorMessage</div>"; ?>
    </div>
    <script src="js/addMovie.js"></script>
</body>
</html>
