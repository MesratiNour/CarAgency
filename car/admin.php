<?php
include 'db.php';

// Handle car management actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_car'])) {
        // Add car
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $registration = $_POST['registration'];
        $availability = isset($_POST['availability']) ? 1 : 0;

        $targetDir = "car_images/";
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Insert car details into database
                $sql = "INSERT INTO Voitures (Marque, Modèle, Année, Immatriculation, Disponibilité, Image) 
                        VALUES ('$brand', '$model', '$year', '$registration', '$availability', '$fileName')";
                if ($conn->query($sql) === TRUE) {
                    echo "Car added successfully!";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Error uploading image!";
            }
        } else {
            echo "Invalid file format!";
        }
    } elseif (isset($_POST['delete_car'])) {
        // Delete car
        $car_id = $_POST['car_id'];
        $sql = "DELETE FROM Voitures WHERE ID = '$car_id'";
        if ($conn->query($sql) === TRUE) {
            echo "Car deleted successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['update_car'])) {
        // Update car
        $car_id = $_POST['car_id'];
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $registration = $_POST['registration'];
        $availability = isset($_POST['availability']) ? 1 : 0;

        $sql = "UPDATE Voitures SET Marque = '$brand', Modèle = '$model', Année = '$year', Immatriculation = '$registration', Disponibilité = '$availability' WHERE ID = '$car_id'";
        if ($conn->query($sql) === TRUE) {
            echo "Car updated successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch all cars with rental information
$sql = "SELECT v.ID, v.Marque, v.Modèle, v.Année, v.Immatriculation, v.Disponibilité, v.Image, 
        l.Date_de_Début, l.Date_de_Fin, c.Nom, c.Numéro_de_téléphone
        FROM Voitures v 
        LEFT JOIN Réservations l ON v.ID = l.Voiture_ID 
        LEFT JOIN Clients c ON l.Client_ID = c.ID";
$cars = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin - Car Management</title>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2>Car Management</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <h3>Add New Car</h3>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="brand">Brand:</label>
            <input type="text" class="form-control" id="brand" name="brand" required>
        </div>
        <div class="form-group">
            <label for="model">Model:</label>
            <input type="text" class="form-control" id="model" name="model" required>
        </div>
        <div class="form-group">
            <label for="year">Year:</label>
            <input type="number" class="form-control" id="year" name="year" required>
        </div>
        <div class="form-group">
            <label for="registration">Registration:</label>
            <input type="text" class="form-control" id="registration" name="registration" required>
        </div>
        <div class="form-group">
            <label for="availability">Availability:</label>
            <input type="checkbox" id="availability" name="availability" value="1" checked>
        </div>
        <div class="form-group">
            <label for="image">Car Image:</label>
            <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
        </div>
        <button type="submit" name="add_car" class="btn btn-primary">Add Car</button>
    </form>

    <h3>All Cars</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Year</th>
                <th>Registration</th>
                <th>Availability</th>
                <th>Image</th>
                <th>Rental Details</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($car = $cars->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $car['ID']; ?></td>
                    <td><?php echo $car['Marque']; ?></td>
                    <td><?php echo $car['Modèle']; ?></td>
                    <td><?php echo $car['Année']; ?></td>
                    <td><?php echo $car['Immatriculation']; ?></td>
                    <td><?php echo $car['Disponibilité'] ? 'Available' : 'Rented'; ?></td>
                    <td><img src="car_images/<?php echo $car['Image']; ?>" alt="Car Image" style="width: 100px;"></td>
                    <td>
                        <?php if (!$car['Disponibilité']) { ?>
                            <p><strong>Rented by:</strong> <?php echo $car['Nom']; ?></p>
                            <p><strong>Phone:</strong> <?php echo $car['Numéro_de_téléphone']; ?></p>
                            <p><strong>From:</strong> <?php echo $car['Date_de_Début']; ?></p>
                            <p><strong>To:</strong> <?php echo $car['Date_de_Fin']; ?></p>
                        <?php } ?>
                    </td>
                    <td>
                        <form method="POST" action="" style="display:inline-block;">
                            <input type="hidden" name="car_id" value="<?php echo $car['ID']; ?>">
                            <button type="submit" name="delete_car" class="btn btn-danger">Delete</button>
                        </form>
                        <button class="btn btn-info" data-toggle="modal" data-target="#updateModal<?php echo $car['ID']; ?>">Update</button>
                    </td>
                </tr>

                <!-- Update Modal -->
                <div class="modal fade" id="updateModal<?php echo $car['ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel<?php echo $car['ID']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateModalLabel<?php echo $car['ID']; ?>">Update Car</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="car_id" value="<?php echo $car['ID']; ?>">
                                    <div class="form-group">
                                        <label for="brand">Brand:</label>
                                        <input type="text" class="form-control" id="brand" name="brand" value="<?php echo $car['Marque']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="model">Model:</label>
                                        <input type="text" class="form-control" id="model" name="model" value="<?php echo $car['Modèle']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="year">Year:</label>
                                        <input type="number" class="form-control" id="year" name="year" value="<?php echo $car['Année']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="registration">Registration:</label>
                                        <input type="text" class="form-control" id="registration" name="registration" value="<?php echo $car['Immatriculation']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="availability">Availability:</label>
                                        <select class="form-control" id="availability" name="availability" required>
                                            <option value="1" <?php if ($car['Disponibilité'] == 1) echo 'selected'; ?>>Available</option>
                                            <option value="0" <?php if ($car['Disponibilité'] == 0) echo 'selected'; ?>>Rented</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_car" class="btn btn-primary">Update Car</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
