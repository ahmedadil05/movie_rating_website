<?php
session_start();
require 'php/db_connect.php'; // Include database connection

// Restrict access to logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect guests to the login page
    exit();
}

// Fetch the current user's data
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT Username, Email FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/settings.css">
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
              <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
              <li><a href="favourite.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'favourite.php' ? 'active' : ''; ?>">Favourites</a></li>
              <li><a href="category.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'category.php' ? 'active' : ''; ?>">Category</a></li>
              <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'): ?>
                <li><a href="addmovie.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'addmovie.php' ? 'active' : ''; ?>">Add a Movie</a></li>
              <?php endif; ?>
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
            <h2>Settings</h2>

            <!-- Update Profile Info -->
            <section class="settings-section">
                <h3>Update Profile Info</h3>
                <form action="php/updateProfile.php" method="POST" class="forms">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['Username']); ?>" placeholder="Enter new username">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" placeholder="Enter new email">
                    </div>
                    <button type="submit" class="update-btn">Save Changes</button>
                </form>
            </section>

            <!-- Update Password -->
            <section class="settings-section">
                <h3>Update Password</h3>
                <form action="php/updatePassword.php" method="POST" class="forms">
                    <div class="form-group">
                        <label for="current-password">Current Password</label>
                        <input type="password" id="current-password" name="current_password" placeholder="Enter current password" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password">New Password</label>
                        <input type="password" id="new-password" name="new_password" placeholder="Enter new password" required>
                    </div>
                    <button type="submit" class="update-btn">Update Password</button>
                </form>
            </section>

            <!-- Delete Account -->
            <section class="settings-section">
              <h3>Delete Account</h3>
              <form id="delete-form" action="php/deleteAccount.php" method="POST">
                  <div class="form-group">
                      <label for="delete-password">Password</label>
                      <input type="password" id="delete-password" name="password" placeholder="Enter your password" required>
                  </div>
                  <button type="button" id="deletebtn" class="delete-btn">Delete Account</button>
              </form>
          </section>
        </main>
        <div id="success-modal" class="modal hidden">
          <div class="modal-content">
            <h2 id="modal-message"></h2>
            <button id="modal-close-btn" class="btn">Close</button>
          </div>
        </div>
        <!-- Delete Confirmation Modal -->
        <div id="confirmation-modal" class="modal hidden">
            <div class="modal-content">
                <h2>Confirm Account Deletion</h2>
                <p>Are you sure you want to delete your account?</p>
                <div class="modal-buttons">
                    <button id="confirm-yes-delete" class="btn">Yes</button>
                    <button id="confirm-no-delete" class="btn">No</button>
                </div>
            </div>
        </div>

    </div>
    <script src="js/settings.js"></script>
</body>
</html>
