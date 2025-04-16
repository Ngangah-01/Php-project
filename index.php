<?php
session_start(); // Start the session
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
require 'connection.php'; // Include the database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="./res/css/home.css">
</head>

<body class="raleway-font">
    <?php if (isset($_SESSION['message'])): ?>
        <ul>
            <li><?php echo $_SESSION['message'];
                unset($_SESSION['message']); ?></li>
        </ul>
    <?php endif; ?>
    <div class="home-container">
        <div class="left">
            <h2 style="margin-top: 2px;">RippleTee</h2>
            <hr>
            <ul>
                <li><a href="index.php" style="color: inherit; text-decoration: none;">Home</a></li>
                <li><a href="profile.php" style="color: inherit; text-decoration: none;">Profile</a></li>
                <li><a href="#" style="color: inherit; text-decoration: none;">Explore</a></li>
                <li><a href="#" style="color: inherit; text-decoration: none;">Messages</a></li>
                <li><a href="#" style="color: inherit; text-decoration: none;">Notification</a></li>
                <li><a href="#" style="color: inherit; text-decoration: none;">Settings</a></li>
                <li><a href="logout.php" style="color: Red; text-decoration: none;">Log out</a></li>
            </ul>
        </div>
        <div class="middle">
            <h2 style="margin-top: 2px; color: white;">Home</h2>
            <hr>
            <div class="posts">
                <h3>Recent Posts</h3>
                <?php
                // Fetch all posts from the database
                $stmt = $conn->prepare("SELECT posts.content, posts.type, posts.file_path, posts.created_at, users.username, users.profile_picture FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($post = $result->fetch_assoc()) {
                        echo "<div class='post'>";
                        echo "<div class='post-header'>";
                        echo "<img src='./res/images/" . htmlspecialchars($post['profile_picture']) . "' alt='Profile Picture' class='post-profile-pic'>";
                        echo "<span class='post-username'>" . htmlspecialchars($post['username']) . "</span>";
                        echo "</div>";
                        echo "<p>" . htmlspecialchars($post['content']) . "</p>";

                        // Display the file based on the post type
                        if ($post['type'] === 'image') {
                            echo "<img src='" . htmlspecialchars($post['file_path']) . "' alt='Post Image' class='post-media'>";
                        } elseif ($post['type'] === 'video') {
                            echo "<video controls class='post-media'>";
                            echo "<source src='" . htmlspecialchars($post['file_path']) . "' type='video/mp4'>";
                            echo "Your browser does not support the video tag.";
                            echo "</video>";
                        } elseif ($post['type'] === 'audio') {
                            echo "<audio controls class='post-media'>";
                            echo "<source src='" . htmlspecialchars($post['file_path']) . "' type='audio/mpeg'>";
                            echo "Your browser does not support the audio tag.";
                            echo "</audio>";
                        }

                        echo "<small>Posted on: " . $post['created_at'] . "</small>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No posts yet.</p>";
                }
                ?>
            </div>
        </div>
        <div class="right">
            <h3 style="margin-top: 2px; color:white;">Additional Info</h3>
            <hr>
            <p>This section can be used for widgets, ads, or extra content.</p>
        </div>
    </div>
</body>

</html>