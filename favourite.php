<?php
session_start();
require 'php/db_connect.php'; // Ensure this connects to your database

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch favorite movies for the logged-in user
$stmt = $conn->prepare("
    SELECT Movies.MovieID, Movies.Title, Movies.Genre, Movies.ReleaseDate, Movies.poster_path
    FROM Movies
    INNER JOIN WatchList ON Movies.MovieID = WatchList.MovieID
    WHERE WatchList.UserID = ?
    ORDER BY Movies.Title ASC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>favourite</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/favourite.css">
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
            <li><a href="favourite.php"  class="active">Favourites</a></li>
            <li><a href="category.php">Category</a></li>
            <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'): ?><li><a href="addmovie.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'addmovie.php' ? 'active' : ''; ?>">Add a Movie</a></li><?php endif; ?>

        </ul>
      </nav>
      <div class="bottom-menu">
        <ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Settings</a></li>
        <?php endif; ?>
          <li><a href="php/logout.php">
              <?php echo isset($_SESSION['username']) ? 'Logout' : 'Login'; ?>
            </a></li>
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
      <section class="favorites">
      <h1>Favourites</h1>
      <div class="favorites-grid">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <a href="moviePage.php?id=<?php echo $row['MovieID']; ?>" class="card" style="background-image: url('<?php echo $row['poster_path']; ?>');">
              <div class="card-text">
                <h3><?php echo $row['Title']; ?></h3>
                <p><?php echo $row['ReleaseDate']; ?> | <?php echo $row['Genre']; ?></p>
              </div>
            </a>
          <?php endwhile; ?>
        <?php else: ?>
          <p>You have no favorite movies yet.</p>
        <?php endif; ?>
      </div>
    </section>

      
    </main>
  </div>

  <script src="js/favourite.js"></script>
</body>
</html>
