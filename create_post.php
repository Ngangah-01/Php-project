<?php
session_start();
require 'connection.php'; // Include the database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $username = $_SESSION['username'];
    $file = $_FILES['file'];
    $file_path = null;
    $type = 'text'; // Default post type

    // Fetch the user's ID from the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $user_id = $user['id'];

        // Handle file upload
        if ($file['error'] === UPLOAD_ERR_OK) {
            $target_dir = "./res/uploads/";
            $file_name = uniqid() . "_" . basename($file['name']);
            $file_path = $target_dir . $file_name;
            $file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

            // Determine the post type based on the file extension
            if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
                $type = 'image';
            } elseif (in_array($file_type, ['mp4', 'mov', 'avi'])) {
                $type = 'video';
            } elseif (in_array($file_type, ['mp3', 'wav'])) {
                $type = 'audio';
            }

            // Move the uploaded file to the target directory
            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                $_SESSION['message'] = "Error uploading file.";
                header("Location: profile.php");
                exit();
            }
        }

        // Insert the post into the database
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, type, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $content, $type, $file_path);
        $stmt->execute();

        // Increment the post count in the users table
        $stmt = $conn->prepare("UPDATE users SET post_count = post_count + 1 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $_SESSION['message'] = "Post created successfully.";
    } else {
        $_SESSION['message'] = "Error: User not found.";
    }

    header("Location: profile.php");
    exit();
}
?>