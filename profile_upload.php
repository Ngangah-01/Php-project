<?php
session_start();
require 'connection.php'; // Include the database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $bio = $_POST['bio'];
    $profile_picture = $_FILES['profile_picture'];

    // Handle file upload
    if ($profile_picture['error'] === UPLOAD_ERR_OK) {
        $target_dir = "./res/images/";
        $target_file = $target_dir . basename($profile_picture['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($profile_picture['tmp_name']);
        if ($check === false) {
            $_SESSION['message'] = "File is not an image.";
            header("Location: profile.php");
            exit();
        }

        // Check file size (max 5MB)
        if ($profile_picture['size'] > 5000000) {
            $_SESSION['message'] = "File is too large.";
            header("Location: profile.php");
            exit();
        }

        // Allow only certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $_SESSION['message'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            header("Location: profile.php");
            exit();
        }

        // Upload the file
        if (move_uploaded_file($profile_picture['tmp_name'], $target_file)) {
            // Update the database with the new profile picture path
            $stmt = $conn->prepare("UPDATE users SET username = ?, bio = ?, profile_picture = ? WHERE username = ?");
            $stmt->bind_param("ssss", $username, $bio, basename($profile_picture['name']), $_SESSION['username']);
            $stmt->execute();

            $_SESSION['username'] = $username; // Update session username
            $_SESSION['message'] = "Profile updated successfully.";
        } else {
            $_SESSION['message'] = "Error uploading file.";
        }
    } else {
        // Update the database without changing the profile picture
        $stmt = $conn->prepare("UPDATE users SET username = ?, bio = ? WHERE username = ?");
        $stmt->bind_param("sss", $username, $bio, $_SESSION['username']);
        $stmt->execute();

        $_SESSION['username'] = $username; // Update session username
        $_SESSION['message'] = "Profile updated successfully.";
    }

    header("Location: profile.php");
    exit();
}
?>