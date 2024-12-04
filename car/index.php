q<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer l'ID du client depuis la session
$user_id = $_SESSION['user_id'];

// Requête SQL pour récupérer toutes les voitures
$sql_cars = "SELECT * FROM Voitures";
$cars = $conn->query($sql_cars);

// Vérifier si le formulaire de recherche a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $date_debut = $_POST['Date_de_début'];
    $date_fin = $_POST['Date_de_fin'];

    // Vérifier si les dates sont valides
    if ($date_debut && $date_fin && $date_debut <= $date_fin) {
        // Requête SQL pour récupérer les voitures disponibles pour la période spécifiée
        $sql_search = "SELECT * FROM Voitures WHERE ID NOT IN 
                       (SELECT Voiture_ID FROM réservations WHERE 
                       (Date_de_début <= '$date_fin' AND Date_de_fin >= '$date_debut') OR 
                       (Date_de_début >= '$date_debut' AND Date_de_fin <= '$date_fin') OR
                       (Date_de_début <= '$date_debut' AND Date_de_fin >= '$date_fin'))";
        
        $cars = $conn->query($sql_search);
    } else {
        // Gérer l'erreur de dates invalides
        echo "Veuillez sélectionner des dates valides pour la recherche.";
    }
}

// Handle car rental
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rent'])) {
    $Voiture_ID = $_POST['Voiture_ID'];
    $Date_de_début = $_POST['Date_de_début'];
    $Date_de_fin = $_POST['Date_de_fin'];

    // Insertion des détails de la réservation dans la table réservations
    $sql_rent = "INSERT INTO réservations (Client_ID, Voiture_ID, Date_de_début, Date_de_fin) 
                 VALUES ('$user_id', '$Voiture_ID', '$Date_de_début', '$Date_de_fin')";
    if ($conn->query($sql_rent) === TRUE) {
        // Mise à jour de la disponibilité de la voiture (à commenter si vous ne voulez pas que la voiture disparaisse de la liste)
        $update_sql = "UPDATE Voitures SET Disponibilité = 0 WHERE ID = '$Voiture_ID'";
        $conn->query($update_sql);
        // Utilisation de JavaScript pour afficher un message d'alerte
        echo "<script>alert('Car rented successfully!');</script>";
    } else {
        echo "Error: " . $sql_rent . "<br>" . $conn->error;
    }
}
?>
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Car Rental</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
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

        .container {
            margin-top: 80px;
        }

        .search-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="banner">
    <div class="banner-content">
        <div class="contact-info">
            <span>Email: contact@example.com</span>
            <span>Phone: +123 456 7890</span>
        </div>
        <div class="banner-text">
            Rent a car and enjoy the open road
        </div>
        <div class="contact-info">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>
<div class="container">
<form class="search-form" method="POST" action="">
        <div class="row">
            <div class="col-md-3">
                <input type="date" class="form-control" name="Date_de_début" placeholder="Start Date">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" name="Date_de_fin" placeholder="End Date">
            </div>
        </div>
        <button type="submit" name="search" class="btn btn-primary mt-3">Search</button>
    </form>
    <h2>All Cars</h2>
    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) { ?>
        
    <?php } ?>
    <div class="row">
        <?php while($car = $cars->fetch_assoc()) { ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="car_images/<?php echo $car['Image']; ?>" class="card-img-top" alt="Car Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $car['Marque'] . ' ' . $car['Modèle']; ?></h5>
                        <p class="card-text">Year: <?php echo $car['Année']; ?></p>
                        <p class="card-text">Registration: <?php echo $car['Immatriculation']; ?></p>
                        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) { ?>
                            <form method="POST" action="">
                                <input type="hidden" name="Voiture_ID" value="<?php echo $car['ID']; ?>">
                                <div class="form-group">
                                    <label for="Date_de_début">Start Date:</label>
                                    <input type="date" class="form-control" id="Date_de_début" name="Date_de_début" required>
                                </div>
                                <div class="form-group">
                                    <label for="Date_de_fin">End Date:</label>
                                    <input type="date" class="form-control" id="Date_de_fin" name="Date_de_fin" required>
                                </div>
                                <button type="submit" name="rent" class="btn btn-primary">Rent</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
