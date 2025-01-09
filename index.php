<?php
session_start();
include './php/db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
  header("Location: ./php/login.php");
  exit();
}

$message = "";

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['content'])) {
  $title = $conn->real_escape_string($_POST['title']);
  $content = $conn->real_escape_string($_POST['content']);
  $userID = $_SESSION['userID']; // Assuming the user is logged in

  $sql = "INSERT INTO posts (userID, title, content) VALUES ('$userID', '$title', '$content')";

  if ($conn->query($sql) === TRUE) {
    $message = "Post submitted successfully.";
    // Redirect to avoid form resubmission
    header("Location: index.php?message=" . urlencode($message));
    exit();
  } else {
    $message = "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && isset($_POST['postID'])) {
  $comment = $conn->real_escape_string($_POST['comment']);
  $postID = $conn->real_escape_string($_POST['postID']);
  $userID = $_SESSION['userID']; // Assuming the user is logged in

  $sql = "INSERT INTO comments (postID, userID, comment) VALUES ('$postID', '$userID', '$comment')";

  if ($conn->query($sql) === TRUE) {
    $message = "Comment submitted successfully.";
    // Redirect to avoid form resubmission
    header("Location: index.php?message=" . urlencode($message));
    exit();
  } else {
    $message = "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deletePostID'])) {
  $deletePostID = $conn->real_escape_string($_POST['deletePostID']);
  
  // Delete associated comments first
  $sql = "DELETE FROM comments WHERE postID='$deletePostID'";
  if ($conn->query($sql) === TRUE) {
    // Now delete the post
    $sql = "DELETE FROM posts WHERE postID='$deletePostID'";
    if ($conn->query($sql) === TRUE) {
      $message = "Post deleted successfully.";
      // Redirect to avoid form resubmission
      header("Location: index.php?message=" . urlencode($message));
      exit();
    } else {
      $message = "Error: " . $sql . "<br>" . $conn->error;
    }
  } else {
    $message = "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Handle comment deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteCommentID'])) {
  $deleteCommentID = $conn->real_escape_string($_POST['deleteCommentID']);
  $sql = "DELETE FROM comments WHERE commentID='$deleteCommentID'";

  if ($conn->query($sql) === TRUE) {
    $message = "Comment deleted successfully.";
    // Redirect to avoid form resubmission
    header("Location: index.php?message=" . urlencode($message));
    exit();
  } else {
    $message = "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Fetch all blog posts
$sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.userID = users.userID ORDER BY upload_time DESC";
$result = $conn->query($sql);

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
  <link rel="icon" type="image/x-icon" href="./assets/favicon-black.ico" media="(prefers-color-scheme: white)">
  <link rel="icon" type="image/x-icon" href="./assets/favicon-white.ico" media="(prefers-color-scheme: dark)">
  <link rel="stylesheet" href="./styles/dist/css/style.css">
  <title>Blog | Home</title>
</head>
<body class="landing">
  
  <i class="fa-sharp fa-solid fa-plus plus-icon" title="post a blog"></i>
  <a href="./php/logout.php"><i class="fa-solid fa-arrow-right-from-bracket logout-icon"></i></a>
  
  <div class="popup-container">
    <form class="popup-form" action="index.php" method="post">
      <i class="fa-solid fa-sharp fa-xmark close-icon"></i>
      <h2>Post a blog</h2>
      <div class="input-wrapper">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" required>
      </div>
      <div class="input-wrapper">
        <label for="content">Content</label>
        <textarea name="content" id="content" required></textarea>
      </div>
      <button class="post-btn">Post</button>
    </form>
  </div>

  <?php if (isset($_GET['message'])): ?>
    <script>
      alert("<?php echo htmlspecialchars($_GET['message']); ?>");
      // Remove the message parameter from the URL
      if (history.replaceState) {
        history.replaceState(null, null, window.location.pathname);
      }
    </script>
  <?php endif; ?>

  <div class="blog-container">
    <?php while ($row = $result->fetch_assoc()): ?>
      <?php
      // Fetch comments for the current post
      $postID = $row['postID'];
      $commentSql = "SELECT comments.*, users.username FROM comments JOIN users ON comments.userID = users.userID WHERE postID = '$postID' ORDER BY comment_time ASC";
      $commentResult = $conn->query($commentSql);
      $commentCount = $commentResult->num_rows;
      ?>
      <div class="blog-post">
        <h2 class="blog-title"><?php echo htmlspecialchars($row['title']); ?></h2>
        <p class="blog-content"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
        <p class="upload-time"><?php echo htmlspecialchars($row['upload_time']); ?></p>
        <p class="uploaded-by">Uploaded by: <?php echo htmlspecialchars($row['username']); ?></p>
        <p class="comment-opener">comments (<span class="comment-amount"><?php echo $commentCount; ?></span>) <i class="fa-sharp fa-solid fa-chevron-down"></i></p>
        <div class="comment-section" style="display: <?php echo $commentCount > 0 ? 'flex' : 'none'; ?>;">
          <?php if ($commentCount > 0): ?>
            <?php while ($commentRow = $commentResult->fetch_assoc()): ?>
              <div class="comment">
                <p class="comment-username"><?php echo htmlspecialchars($commentRow['username']); ?></p>
                <p class="comment-text"><?php echo nl2br(htmlspecialchars($commentRow['comment'])); ?></p>
                <p class="comment-time"><?php echo htmlspecialchars($commentRow['comment_time']); ?></p>
                <?php if ($_SESSION['status'] == 'admin'): ?>
                  <form class="delete-comment-form" action="index.php" method="post">
                    <input type="hidden" name="deleteCommentID" value="<?php echo $commentRow['commentID']; ?>">
                    <button class="delete-btn" title="Delete Comment"><i class="fa-solid fa-trash"></i></button>
                  </form>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>
          <form class="comment-form" action="index.php" method="post">
            <input type="hidden" name="postID" value="<?php echo $postID; ?>">
            <input type="hidden" name="userID" value="<?php echo $_SESSION['userID']; ?>">
            <textarea name="comment" placeholder="Write a comment..." required></textarea>
            <button class="comment-btn">Comment</button>
          </form>
        </div>
        <?php if ($_SESSION['status'] == 'admin'): ?>
          <form class="delete-post-form" action="index.php" method="post">
            <input type="hidden" name="deletePostID" value="<?php echo $postID; ?>">
            <button class="delete-btn" title="Delete Post"><i class="fa-solid fa-trash"></i></button>
          </form>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  </div>

  <script src="./js/general.js"></script>
</body>
</html>

<?php
$conn->close();
?>