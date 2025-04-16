<?php
session_start();
include 'connection.php';

if (isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirect to homepage if already logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if($stmt->num_rows > 0 && password_verify($password ,$hashed_password)){
        $_SESSION['username'] = $username;
        $_SESSION['User_id'] = $id;
        header("Location: index.php");
        exit();
    }
    else{
        $error = "Invalid Username or password!!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./res/css/style.css">
</head>
<body>
<div class="container_login">
        <!-- Login Section -->
        <div class="image_div_login">
            <img src="./res/images/photo2.jpg" alt="RippleTee Logo" >
            <div class="overlay">
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Illo dignissimos temporibus voluptatum aperiam possimus, omnis vel debitis deserunt id sint!</p>
            </div>
        </div>
        <div class="login">
            <h2 id="title">RippleTee</h2>
            <h3 id="title">Log into your account</h3>
            <form action="login.php" method="post">
            <?php if(!empty($error)):?>
                    <div class="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <label for="input">Username</label><br>
                <input type="text" id="input" name="username" required><br>
                
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password" required><br>
                <button id="btn">LOG IN</button><br>
                <p id="p">Do not have an account? <a href="Register.php">Sign up</a></p>
            </form>
        </div>
    </div>
</body>
</html>