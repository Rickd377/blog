<?php
include 'db_conn.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $conn->real_escape_string($_POST['username']);
  $email = $conn->real_escape_string($_POST['email']);
  $password = $conn->real_escape_string($_POST['password']);
  $confirmPassword = $conn->real_escape_string($_POST['confirm-password']);

  if ($password !== $confirmPassword) {
    $message = "Passwords do not match.";
  } else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Get the lowest available userID
    $result = $conn->query("SELECT MIN(userID + 1) AS nextID FROM users WHERE (userID + 1) NOT IN (SELECT userID FROM users)");
    $row = $result->fetch_assoc();
    $nextID = $row['nextID'] ?? 1;

    $sql = "INSERT INTO users (userID, username, email, password) VALUES ('$nextID', '$username', '$email', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
      header("Location: ../index.php");
      exit();
    } else {
      $message = "Error: " . $sql . "<br>" . $conn->error;
    }
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta author="Rick Deurloo">
  <meta name="description" content="Website description">
  <meta name="keywords" content="Website keywords">

  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/sharp-thin.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/sharp-solid.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/sharp-regular.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/sharp-light.css">

  <link rel="icon" type="image/x-icon" href="../assets/favicon-black.ico" media="(prefers-color-scheme: white)">
  <link rel="icon" type="image/x-icon" href="../assets/favicon-white.ico" media="(prefers-color-scheme: dark)">
  <link rel="stylesheet" href="../styles/dist/css/style.css">

  <title>Blog | Register</title>
</head>
<body class="authentication">
  <form class="form-register" action="register.php" method="post">
    <h2 class="title">Register</h2>
    <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
    <div class="input-wrapper">
      <input type="text" name="username" id="username" placeholder="Username" required>
      <i class="fa-light fa-user icon"></i>
      <label for="username">Username</label>
    </div>
    <div class="input-wrapper">
      <input type="email" name="email" id="email" placeholder="Email address" required>
      <i class="fa-light fa-envelope icon"></i>
      <label for="email">Email address</label>
    </div>
    <div class="input-wrapper">
      <input type="password" name="password" id="password" placeholder="Password" required>
      <i class="fa-light fa-eye-slash icon"></i>
      <label for="password">Password</label>
    </div>
    <div class="input-wrapper">
      <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm password" required>
      <i class="fa-light fa-eye-slash icon"></i>
      <label for="confirm-password">Confirm password</label>
    </div>
    <button class="submit-btn">Register</button>
    <p class="account-text">Already have an account? <a href="login.php">Login</a></p>
  </form>
  <script src="../js/general.js"></script>
</body>
</html>