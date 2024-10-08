<?php
session_start();
include 'config.php';

// Initialize username
$username = '';

if (isset($_SESSION['ID'])) {
    $stmt = $conn->prepare("SELECT CustomerName FROM Customer WHERE CustomerID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['ID']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $username = $row['CustomerName'];
        }
        $stmt->close();
    }
}

// Fetch service details
$serviceIds = [1, 2, 3];
$services = [];

foreach ($serviceIds as $id) {
    $stmt = $conn->prepare("SELECT serviceid, servicename, description, price, availability FROM service WHERE serviceid = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $services[$id] = $result->fetch_assoc();
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/services.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome -->
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

<div class="container">
    <div class="firstphoto">
        <img src="images/dog.jpeg" alt="Photo">
    </div>

    <!-- Grooming -->
    <div class="section" id="grooming">
        <h2><?php echo htmlspecialchars($services[1]['servicename']); ?></h2>
        <div class="box">
            <?php if (isset($services[1])): ?>
                <img src="images/dog.jpeg" alt="Grooming Service">
                <div class="box-content">
                    <p><?php echo htmlspecialchars($services[1]['description']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($services[1]['price']); ?></p>
                    <a href="bookingForm.php?serviceID=<?php echo urlencode($services[1]['serviceid']); ?>" class="button">Book Now</a>

                </div>
            <?php else: ?>
                <p>No grooming services available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Veterinary Care -->
    <div class="section" id="veterinary-care">
    <h2><?php echo htmlspecialchars($services[2]['servicename']); ?></h2>
        <div class="box">
            <?php if (isset($services[2])): ?>
                <img src="images/dog.jpeg" alt="Veterinary Care Service">
                <div class="box-content">
                    <p><?php echo htmlspecialchars($services[2]['description']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($services[2]['price']); ?></p>
                    <a href="bookingForm.php?serviceID=<?php echo urlencode($services[2]['serviceid']); ?>" class="button">Book Now</a>

                </div>
            <?php else: ?>
                <p>No veterinary services available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pet Boarding -->
    <div class="section" id="petboarding">
        <h2><?php echo htmlspecialchars($services[3]['servicename']); ?></h2>
        <div class="box">
            <?php if (isset($services[3])): ?>
                <img src="images/dog.jpeg" alt="Pet Boarding Service">
                <div class="box-content">
                    <p><?php echo htmlspecialchars($services[3]['description']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($services[3]['price']); ?></p>
                    <a href="bookingForm.php?serviceID=<?php echo urlencode($services[3]['serviceid']); ?>" class="button">Book Now</a>

                </div>
            <?php else: ?>
                <p>No pet boarding services available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function showUserInfo() {
        $('#userInfoModal').modal('show');
    }
</script>

</body>
</html>
