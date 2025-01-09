<?php
session_start();
include 'db_conn.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $usernameEmail = $conn->real_escape_string($_POST['username-email']);
  $password = $conn->real_escape_string($_POST['password']);

  $sql = "SELECT * FROM users WHERE username='$usernameEmail' OR email='$usernameEmail'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
      $_SESSION['userID'] = $row['userID'];
      $_SESSION['username'] = $row['username'];
      $_SESSION['status'] = $row['status'];

      header("Location: ../index.php");
      exit();
    } else {
      $message = "Invalid password.";
    }
  } else {
    $message = "Incorrect username or email address.";
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

  <title>Blog | Login</title>
</head>
<body class="authentication">
  <form class="form-register" action="login.php" method="post">
    <h2 class="title">Login</h2>
    <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
    <div class="input-wrapper">
      <input type="text" name="username-email" id="username-email" placeholder="Username or email address" required>
      <i class="fa-light fa-user icon"></i>
      <label for="username-email">Username or email address</label>
    </div>
    <div class="input-wrapper">
      <input type="password" name="password" id="password-login" placeholder="Password" required>
      <i class="fa-light fa-eye-slash icon"></i>
      <label for="password">Password</label>
    </div>
    <button class="submit-btn">Login</button>
    <p class="account-text">Don't have an account yet? <a href="register.php">Register</a></p>
  </form>
  <script src="../js/general.js"></script>
</body>
</html>