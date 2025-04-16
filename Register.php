<?php
session_start();
include 'connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirect to homepage if already logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Check if all required fields are set
    if (!isset($_POST["username"]) || !isset($_POST["email"]) || !isset($_POST["password"])) {
        die("All fields are required. Please go back and fill in all fields.");
    }
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $hashed_password = password_hash($password,PASSWORD_BCRYPT);

    //checking if the username or email already exists before inserting
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        $error = "⚠️Username or Email already exists";
    }else{
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES(?,?,?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if($stmt->execute()){
            $_SESSION['username'] =$username;
            $_SESSION['user_id'] = $stmt->insert_id;

            header("Location: index.php?success=1");
            $sms = "✅Account created successfully";
            exit();
        }else{
            echo "Error: ".$stmt->error;
        }
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./res/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Register Section -->
        <div class="login">
            <h2 id="title">RippleTee</h2>
            <h3 id="title">Create an account</h3>
            <form action="register.php" method="post">
                <?php if(!empty($error)):?>
                    <div class="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <label for="input">Username</label><br>
                <input type="text" id="input" name="username" required><br>
                <label for="Email">Email</label><br>
                <input type="email" id="Email" name="email" required><br>
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password" required><br>
                <button id="btn">SIGN UP</button><br>
                <p id="p">Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
        <div class="image_div">
            <img src="./res/images/photo2.jpg" alt="RippleTee Logo" >
            <div class="overlay">
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Illo dignissimos temporibus voluptatum aperiam possimus, omnis vel debitis deserunt id sint!</p>
            </div>
        </div>
    </div>
</body>
</html>