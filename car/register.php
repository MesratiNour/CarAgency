register.php



<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include 'db.php';




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO Clients (Nom, Adresse, Numéro_de_téléphone, Email, Mot_de_passe) VALUES ('$name', '$address', '$phone', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap.css" rel="stylesheet">
    <title>Register</title>
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
            text-align: center;
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
            <span>Email: contact@example.com</span>
            <span>Phone: +123 456 7890</span>
        </div>
        <div class="banner-text">
            Rent a car and enjoy the open road
        </div>
        <div class="contact-info"></div>
    </div>
</div>
<div class="content">
    <div id="registerForm" class="form-container inline-form-container">
        
        <h4 class="mb-3 text-center">Register</h4>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary">Register</button>
        </form>
        <p class="mt-3 text-center">Already have an account? <a href="login.php" style="color: #fff;">Login here</a></p>
    </div>
</div>
</body>
</html>