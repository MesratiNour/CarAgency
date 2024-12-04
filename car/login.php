<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user is an admin based on their email address
    $isAdmin = false;
    if ($email === 'admin@gmail.com') {
        $isAdmin = true;
    }

    // Perform authentication based on user type (admin or regular user)
    if ($isAdmin) {
        // Admin authentication
        if ($email === 'admin@gmail.com' && $password === 'adminadmin') {
            $_SESSION['nom'] = 'Admin';
            header("Location: admin.php"); // Redirect to admin page after successful login
            exit();
        } else {
            echo "Invalid credentials for admin.";
        }
    } else {
        // Regular user authentication
        $sql = "SELECT * FROM Clients WHERE Email = '$email'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['Mot_de_passe'])) {
                $_SESSION['user_id'] = $row['ID'];
                header("Location: index.php");
                exit();
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "No user found with this email!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap.css" rel="stylesheet">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            overflow: hidden;
            flex-direction: column;
        }

        .video-background {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
        }

        .banner {
            background-color: white;
            padding: 10px 20px;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .banner-content {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-right: auto;
            font-size: 18px;
        }

        .contact-info span {
            font-size: 18px;
            color: #333;
        }

        .banner-text {
       
            margin-left:260px;
            flex-grow: 1;
            font-size: 30px; /* Taille de texte plus grande */
        }

        .content {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin-top: 80px;
        }

        .form-container {
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
        }

        .btn {
            width: 100%;
            display: block;
        }

        .inline-form-container {
            display: inline-block;
        }
    </style>
</head>
<body>
<video class="video-background" autoplay muted loop>
    <source src="video.mp4" type="video/mp4">
</video>
<div class="banner">
    <div class="banner-content">
        <div class="contact-info">
            <span>Email: RentACar@gmail.com</span>
            <span>Phone: +216 70 456 789</span>
        </div>
        <div class="banner-text">
            Rent a car and enjoy the open road
        </div>
        <div class="contact-info"></div>
    </div>
</div>
<div class="content">
    <div id="loginForm" class="form-container inline-form-container">
        <h4 class="mb-3 text-center">Login</h4>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </form>
        <p class="mt-3 text-center">Don't have an account? <a href="register.php" style="color: #fff;">Register here</a></p>
    </div>
</div>
</body>
</html>


