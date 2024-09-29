<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
            <img src="images/logo.jpg" alt="Pet-Lover-Logo" width=100>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="homepage.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Services</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="serviceDetail.php?serviceID=1">Grooming</a>
                            <a class="dropdown-item" href="serviceDetail.php?serviceID=2">Veterinary Care</a>
                            <a class="dropdown-item" href="serviceDetail.php?serviceID=3">Pet Boarding</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="appointmentHistory.php">Appointment History</a></li>
                </ul>
                <?php if (!empty($username)): ?>
                    <div class="user-profile">
                        <span class="username"><?php echo htmlspecialchars($username); ?></span>
                        <a href="customerinfo.php?ID=<?php echo urlencode($_SESSION['ID']); ?>" class="profile-icon">
                            <img src="https://img.icons8.com/material-outlined/24/000000/user.png" alt="Profile" class="profile-icon">
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>

        <?php
        if (isset($_GET['serviceID'])) {
            $ServiceID = $_GET['serviceID'];  

            // Check which Service ID it is and include the relevant HTML file
            if ($ServiceID == 1) {
                include 'grooming.html'; 
            } else if ($ServiceID == 2) {
                include 'petboarding.html';
            } else if ($ServiceID == 3) {
                include 'vetinarycare.html';
            } else {
                echo "Invalid Service ID!";
            }
        } else {
            echo "Service ID not provided!";
        }

        ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>