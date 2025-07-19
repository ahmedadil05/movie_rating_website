<?php
// Start session and include database connection
session_start();
require 'php/db_connect.php';

// Fetch Hero Movie (Most Visited Movie)
$heroQuery = "SELECT * FROM Movies ORDER BY VisitCount DESC LIMIT 1";
$heroResult = $conn->query($heroQuery);
$heroMovie = $heroResult->fetch_assoc();

// Fetch trending movies
$trendingQuery = "SELECT * FROM Movies ORDER BY VisitCount DESC LIMIT 10";
$trendingResult = $conn->query($trendingQuery);

// Fetch new release movies
$newReleaseQuery = "SELECT * FROM Movies ORDER BY ReleaseDate DESC LIMIT 10";
$newReleaseResult = $conn->query($newReleaseQuery);

// Check if Hero Movie is in the user's wishlist
$isFavorite = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $movieId = $heroMovie['MovieID'];

    $stmt = $conn->prepare("SELECT * FROM WatchList WHERE UserID = ? AND MovieID = ?");
    $stmt->bind_param("ii", $userId, $movieId);
    $stmt->execute();
    $result = $stmt->get_result();
    $isFavorite = $result->num_rows > 0;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/home.css">
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
          <li><a href="index.php" class="active">Home</a></li>
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
          <li>
                <a href="php/logout.php">
              <?php echo isset($_SESSION['username']) ? 'Logout' : 'Login'; ?>
            </a>
          </li>
        </ul>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="content">
      <!-- Header Section -->
      <header>
      <form action="category.php" method="GET" class="search-form">
        <input type="text" name="query" class="search-bar" placeholder="Search..." required>
        <button type="submit" style="display: none;"></button> <!-- Allow pressing Enter -->
      </form>
      <div class="profile" class="profile-link">
          <span class="profile-name">
              <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
          </span>
      </div>


      </header>


      <!-- Hero Section -->
      <section class="hero" style="background-image: url('<?php echo $heroMovie['cover_path']; ?>');">
        <div class="hero-content">
          <h1><?php echo $heroMovie['Title']; ?></h1>
          <p><?php echo $heroMovie['ReleaseDate']; ?> | <?php echo $heroMovie['Genre']; ?></p>
          <div class="hero-buttons">
            <a href="moviePage.php?id=<?php echo $heroMovie['MovieID']; ?>" class="btn-watch">
                Watch now
            </a>
            <button class="btn-favorite <?php echo $isFavorite ? 'active' : ''; ?>" data-movie-id="<?php echo $heroMovie['MovieID']; ?>" id="favorite-btn">
              <i class="fa-solid fa-heart"></i>
            </button>
          </div>
        </div>
      </section>

      <!-- Trending Section -->
      <section class="section trending">
        <h2>Trending</h2>
        <div class="carousel-container">
          <button class="carousel-btn left-btn"><i class="fa-solid fa-arrow-left"></i></button>
          <div class="cards">
            <?php while ($row = $trendingResult->fetch_assoc()): ?>
              <a href="moviePage.php?id=<?php echo $row['MovieID']; ?>" class="card" style="background-image: url('<?php echo $row['poster_path']; ?>');">
                <div class="card-text">
                  <h3><?php echo $row['Title']; ?></h3>
                  <p><?php echo $row['ReleaseDate']; ?> | <?php echo $row['Genre']; ?></p>
                </div>
              </a>
            <?php endwhile; ?>
          </div>
          <button class="carousel-btn right-btn"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </section>

      <!-- New Releases Section -->
      <section class="section continue-watching">
        <h2>New Releases</h2>
        <div class="carousel-container">
          <button class="carousel-btn left-btn"><i class="fa-solid fa-arrow-left"></i></button>
          <div class="cards">
            <?php while ($row = $newReleaseResult->fetch_assoc()): ?>
              <a href="moviePage.php?id=<?php echo $row['MovieID']; ?>" class="card" style="background-image: url('<?php echo $row['poster_path']; ?>');">
                <div class="card-text">
                  <h3><?php echo $row['Title']; ?></h3>
                  <p><?php echo $row['ReleaseDate']; ?> | <?php echo $row['Genre']; ?></p>
                </div>
              </a>
            <?php endwhile; ?>
          </div>
          <button class="carousel-btn right-btn"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </section>
    </main>
  </div>
  <script src="js//home.js"></script>
</body>
</html>
