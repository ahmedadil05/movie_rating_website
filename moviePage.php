<?php
session_start();
require 'php/db_connect.php';

// Get the movie ID from the URL
$movieId = $_GET['id'] ?? null;

if (!$movieId) {
    echo "Movie not found!";
    exit();
}


// Fetch movie details
$stmt = $conn->prepare("SELECT * FROM Movies WHERE MovieID = ?");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$movieResult = $stmt->get_result();

if ($movieResult->num_rows === 0) {
    echo "Movie not found!";
    exit();
}

$movie = $movieResult->fetch_assoc();
$stmt->close();

// Increment VisitCount
$updateStmt = $conn->prepare("UPDATE Movies SET VisitCount = VisitCount + 1 WHERE MovieID = ?");
$updateStmt->bind_param("i", $movieId);
$updateStmt->execute();
$updateStmt->close();

// Fetch reviews for the movie
$reviewStmt = $conn->prepare("SELECT r.*, u.Username FROM Comments r JOIN Users u ON r.UserID = u.UserID WHERE r.MovieID = ?");
$reviewStmt->bind_param("i", $movieId);
$reviewStmt->execute();
$reviews = $reviewStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($movie['Title']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/moviePage.css">
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
              <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Settings</a></li>
          <?php endif; ?>
          <li><a href="php/logout.php"><?php echo isset($_SESSION['username']) ? 'Logout' : 'Login'; ?></a></li>
        </ul>
      </div>
    </aside>



    <div class="movie-page content">

        <!-- Top Right Admin Buttons -->
        <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'): ?>
          <div class="top-right-buttons">
            <button class="edit-btn" data-movie-id="<?php echo $movieId; ?>">Edit</button>
            <button class="delete-btn" data-movie-id="<?php echo $movieId; ?>">Delete</button>
          </div>
        <?php endif; ?>

        <!-- Delete Confirmation Modal -->
        <div id="confirmation-modal" class="modal hidden">
            <div class="modal-content">
                <h2>Confirm Movie Deletion</h2>
                <p>Are you sure you want to delete this movie?</p>
                <div class="modal-buttons">
                    <button id="confirm-yes-delete" class="btn">Yes</button>
                    <button id="confirm-no-delete" class="btn">No</button>
                </div>
            </div>
        </div>
        <!-- Edit Movie Modal -->
        <div id="edit-modal" class="modal hidden">
            <div class="modal-content">
                <h2>Edit Movie Details</h2>
                <form id="edit-movie-form" enctype="multipart/form-data" action="php/edit_movie" method="post">
                    <!-- Hidden input for Movie ID -->
                    <input type="hidden" name="movie_id" />

                    <!-- Form Fields -->
                    <div class="form-section">
                        <div class="form-group">
                            <p>Movie Name</p>
                            <input type="text" name="movie_name" required />
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
                            <input
                                type="text"
                                name="realasedate"
                                required
                                pattern="\d{4}-\d{2}-\d{2}"
                                title="Date must be in the format YYYY-MM-DD"
                                placeholder="YYYY-MM-DD"
                            />
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
                            <input
                                id="movie_poster_input"
                                type="file"
                                name="movie_poster"
                                accept="image/*"
                                style="display: none;"
                                onchange="previewImage(event, 'poster-preview')"
                            />
                            <img id="poster-preview" src="assets/images/plus.png" alt="Poster Preview" />
                        </label>
                        <p>Cover Image</p>
                        <label class="image-button">
                            <input
                                id="cover_image_input"
                                type="file"
                                name="cover_image"
                                accept="image/*"
                                style="display: none;"
                                onchange="previewImage(event, 'cover-preview')"
                            />
                            <img id="cover-preview" src="assets/images/plus.png" alt="Cover Preview" />
                        </label>
                    </div>

                    <div class="modal-buttons">
                        <button type="button" id="close-edit-modal" class="btn">Cancel</button>
                        <button type="submit" id="submit-changes" class="btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>


      <!-- Background Image -->
      <div class="hero-section">
        <img src="<?php echo htmlspecialchars($movie['cover_path']); ?>" alt="<?php echo htmlspecialchars($movie['Title']); ?> Background">
      </div>
  
      <!-- Movie Information -->
      <div class="movie-info">
        <div class="poster">
          <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="<?php echo htmlspecialchars($movie['Title']); ?> Poster">
        </div>
        <div class="details-content">
          <h1><?php echo htmlspecialchars($movie['Title']); ?></h1>
          <div class="trailer-rate">
            <a target="_blank" href="<?php echo htmlspecialchars($movie['TrailerURL']); ?>" class="trailer"><i class="fa-solid fa-video"></i> Trailer</a>
            <span class="rating">IMDb: <span id="rate"><?php echo htmlspecialchars($movie['Rating']); ?></span></span>
          </div>
          <p><?php echo htmlspecialchars($movie['Description']); ?></p>
          <div class="meta">
            <span>Genre: <span class="info"><?php echo htmlspecialchars($movie['Genre']); ?></span></span>
            <span>Director: <span class="info"><?php echo htmlspecialchars($movie['Directors']); ?></span></span>
            <span>Year: <span class="info"><?php echo htmlspecialchars($movie['ReleaseDate']); ?></span></span>
            <span>Duration: <span class="info"><?php echo htmlspecialchars($movie['Duration']); ?> minutes</span></span>
            <span>Actors: <span class="info"><?php echo htmlspecialchars($movie['Actors']); ?></span></span>
          </div>
          <a class="btn-watch" href="<?php echo htmlspecialchars($movie['MovieURL']); ?>">
            <i class="fa-solid fa-play"></i> Watch now
          </a>
            <!-- Favorite Button -->
            <?php 
            $isFavorite = false; // Check from the database or session if this movie is in the user's favorite list
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
                $stmt = $conn->prepare("SELECT * FROM WatchList WHERE UserID = ? AND MovieID = ?");
                $stmt->bind_param("ii", $userId, $movieId);
                $stmt->execute();
                $result = $stmt->get_result();
                $isFavorite = $result->num_rows > 0;
                $stmt->close();
            }
            ?>
          <button class="btn-favorite <?php echo $isFavorite ? 'active' : ''; ?>" data-movie-id="<?php echo $movieId; ?>" id="favorite-btn">
              <i class="fa-solid fa-heart"></i>
          </button>
        </div>
      </div>
  
      <!-- User Reviews -->
      <h2 class="user-review-title">User reviews</h2>
      <div class="reviews">
        <div class="comment-box">
          <textarea placeholder="Add a comment" id="comment-text"></textarea>
          <button class="post-btn" data-movie-id="<?php echo $movieId; ?>">Post</button>
        </div>
      </div>

      <!-- Display Reviews -->
      <section class="user-reviews">
        <h2>All Reviews</h2>
        <div class="reviews-container">
          <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="review-card">
              <div class="user-info">
                <div class="user-details">
                  <h3><?php echo htmlspecialchars($review['Username']); ?></h3>
                </div>
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $review['UserID'] || (isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'))): ?>
                  <i class="fa-solid fa-trash delete-icon" data-review-id="<?php echo $review['CommentID']; ?>"></i>
                <?php endif; ?>

              </div>
              <p class="review-text"><?php echo htmlspecialchars($review['CommentText']); ?></p>
            </div>
          <?php endwhile; ?>
        </div>
      </section>
    </div>
  </div>
  <script src="js/moviePage.js"></script>
</body>
</html>

