<?php
session_start();
require 'connection.php'; // Include the database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user data from the database
try {
    $stmt = $conn->prepare("SELECT id, username, email, profile_picture,bio, post_count FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="./res/css/home.css?v=1">
    <style>
        .raleway-font {
            font-family: 'Raleway', sans-serif;
        }

        .home-container {
            display: flex;
            padding: 20px;
        }

        .profile-left,
        .profile-container {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
        }

        .profile-left ul {
            list-style: none;
            padding: 0;
        }

        .profile-left ul li {
            margin: 10px 0;
        }

        .profile-picture img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
    </style>
</head>

<body class="raleway-font">

    <div class="home-container">
        <div class="profile-left">
            <h2 style="margin-top: 2px;">Profile</h2>
            <hr>
            <ul>
                <li><a href="index.php" style="color: inherit; text-decoration: none;">Home</a></li>
                <li><a href="#" style="color: inherit; text-decoration: none;">Edit profile</a></li>
                <li><a href="#" style="color: inherit; text-decoration: none;">Settings</a></li>
                <li><a href="#" style="color: inherit; text-decoration: none;">Messages</a></li>
                <li><a href="logout.php" style="color: Red; text-decoration: none;">Log out</a></li>
                <li><a href="#" style="color: red; text-decoration: none;">Delete Account</a></li>
            </ul>
        </div>
        <div class="middle profile-container">
            <h2 style="margin-top: 2px;">Your Profile</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="success-message" id="successMessage">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <hr>
            <div class="profile-picture">
                <section class="img-section">
                    <img id="profile-img" src="./res/images/<?php echo $user['profile_picture']; ?>" alt="">
                    <div>
                        <p>Username: <?php echo $user['username']; ?></p>
                        <p>Email: <?php echo $user['email']; ?></p>
                    </div>
                </section>
                <section class="text-section">
                    <h4>User info</h4>
                    <p>Bio : <?php echo $user['bio']; ?></p>
                    <p>Marital Status: Single</p>
                    <p>Date of birth: N/A</p>
                    <p>Location: N/A</p>
                    <p><strong>Total Posts:</strong> <?php echo isset($user['post_count']) ? $user['post_count'] : 0; ?></p>
                    <button id="createPostButton">Create a post</button>
                </section>
            </div>
            <!--Modal for craeting a post -->
            <div id="createPostModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="closeCreatePostModal">&times;</span>
                    <h2>Create a Post</h2>
                    <form id="createPostForm" action="create_post.php" method="post" enctype="multipart/form-data">
                        <textarea name="content" rows="4" placeholder="What's on your mind?" required></textarea>
                        <input type="file" name="file" id="file" accept="image/*, video/*, audio/*">
                        <button type="submit">Post</button>
                    </form>
                </div>
            </div>
            <!-- Modal for Edit Profile -->
            <div id="editProfileModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Edit Profile</h2>
                    <form id="editProfileForm" action="profile_upload.php" method="post" enctype="multipart/form-data">
                        <label for="profile_picture">Profile Picture:</label>
                        <input type="file" name="profile_picture" id="profile_picture"><br><br>

                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" value="<?php echo $user['username']; ?>"><br><br>

                        <label for="bio">Bio:</label>
                        <textarea name="bio" id="bio" rows="4"><?php echo $user['bio']; ?></textarea><br><br>

                        <button type="submit">Save Changes</button>
                    </form>
                </div>
            </div>
            <h3 class="post-title">Your Posts</h3>
            <div class="user-posts">
                <?php
                // Fetch the user's posts from the database
                $stmt = $conn->prepare("SELECT content, type, file_path, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($post = $result->fetch_assoc()) {
                        echo "<div class='post'>";
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
    </div>

    <script src="./res/js/Script.js"></script>
</body>

</html>