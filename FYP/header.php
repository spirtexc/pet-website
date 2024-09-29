<?php
include 'config.php'; // Include the connection file

// Initialize session and fetch username if logged in
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/header.css"> <!-- Ensure the CSS path is correct -->
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
            <img src="images/logo.jpg" alt="Pet-Lover-Logo" width="100">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About Us</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Services
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="#">Grooming</a>
                            <a class="dropdown-item" href="#">Veterinary Care</a>
                            <a class="dropdown-item" href="#">Pet Boarding</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Bookings</a>
                    </li>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>