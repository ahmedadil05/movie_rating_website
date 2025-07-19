<?php
session_start();
require 'db_connect.php'; // Ensure this connects to your database

// Capture the search query
$searchQuery = $_GET['query'] ?? '';

// Fetch movies based on the search query or fetch all movies alphabetically
if ($searchQuery) {
    $stmt = $conn->prepare("SELECT * FROM Movies WHERE Title LIKE ? ORDER BY Title ASC");
    $searchTerm = "%" . $searchQuery . "%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $stmt = $conn->prepare("SELECT * FROM Movies ORDER BY Title ASC");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Category</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/category.css">

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
          <li><a href="../index.php">Home</a></li>
          <li><a href="favourite.html">Favourites</a></li>
          <li><a href="category.php" class="active">Category</a></li>
        </ul>
      </nav>
      <div class="bottom-menu">
        <ul>
          <li>Settings</li>
          <li><a href="php/logout.php">Logout</a></li>
        </ul>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="content">
      <!-- Header Section -->
      <header>
        <form action="category.php" method="GET" class="search-form">
          <input type="text" name="query" class="search-bar" placeholder="Search..." value="<?php echo htmlspecialchars($searchQuery); ?>" required>
          <button type="submit" style="display: none;"></button>
        </form>
        <a href="profile.html" class="profile-link">
          <div class="profile">
            <span class="profile-name"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?></span>
          </div>
        </a>
      </header>

      <!-- Movies Section -->
      <div class="main-content">
        <section class="category">
          <h1><?php echo $searchQuery ? "Search Results for \"$searchQuery\"" : "All Movies"; ?></h1>
          <div class="category-grid">
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
              <p>No movies found matching your search.</p>
            <?php endif; ?>
          </div>
        </section>

        <!-- Genre Filter Section -->
        <aside class="filter">
          <h3>Genre</h3>
          <ul>
            <li>
              <input type="checkbox" id="action" />
              <label for="action">Action</label>
            </li>
            <li>
              <input type="checkbox" id="comedy" />
              <label for="comedy">Comedy</label>
            </li>
            <li>
              <input type="checkbox" id="drama" />
              <label for="drama">Drama</label>
            </li>
            <li>
              <input type="checkbox" id="sci-fi" />
              <label for="sci-fi">Sci-Fi</label>
            </li>
            <li>
              <input type="checkbox" id="documentary" />
              <label for="documentary">Documentary</label>
            </li>
          </ul>
        </aside>
      </div>
    </main>
  </div>

  <script src="../js/category.js"></script>
</body>
</html>
