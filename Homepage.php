<?php
session_start();
include 'config.php';

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
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/homepage.css">
</head>
<body onload="checkError()">
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
 
<body>
    <div class="slideshow-container"> <!-- Slideshow container -->
        <div class="mySlides fade"> <!-- Individual slide -->
            <img src="images/pet-grooming.png" alt="Pet Grooming" style="width:100%">
        </div>

        <div class="mySlides fade"> <!-- Individual slide -->
            <img src="images/pet-boarding.png" alt="Pet Boarding" style="width:100%">
        </div>

        <div class="mySlides fade"> <!-- Individual slide -->
            <img src="images/veterinary-care.png" alt="Veterinary Care" style="width:100%">
        </div>

        <!-- "Book Appointment Now" button -->
        <div class="book-appointment-button" id="bookAppointment">
            <a href="service.php" class="button">Book Appointment Now</a>
        </div>

        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </div>

    <br>

    <div style="text-align:center"> <!-- Dots for slide navigation -->
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
    </div>

    <script>
        var slideIndex = 1;
        var timer = null;
        showSlides(slideIndex);

        function plusSlides(n) {
            clearTimeout(timer);
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            clearTimeout(timer);
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            var dots = document.getElementsByClassName("dot");
            if (n == undefined) { n = ++slideIndex }
            if (n > slides.length) { slideIndex = 1 }
            if (n < 1) { slideIndex = slides.length }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " active";
            timer = setTimeout(showSlides, 4000);
        }
    </script>

<style>
    /* Styling for the book appointment button */
    .book-appointment-button {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
    }

    /* Button styling */
    .button {
        background-color: #4CAF50; /* Green background */
        border: none; /* Remove borders */
        color: white; /* White text */
        padding: 25px 50px; /* Increased padding for larger button */
        text-align: center; /* Center text */
        text-decoration: none; /* Remove underline */
        display: inline-block;
        font-size: 18px; /* Slightly larger font size */
        border-radius: 12px; /* Rounded corners */
        cursor: pointer;
        transition: background-color 0.3s ease; /* Smooth transition for hover */
    }

    .button:hover {
        background-color: #45a049; /* Darker green on hover */
        text-decoration:none;
        color:black;
    }
</style>


    <hr>
    <div class="about-container">
        <div class="row">
            <div class="picture">Picture</div>
            <div class="details">
                <p>Pet Lover is a one-stop pet store dedicated to providing exceptional care for your beloved animals. We offer a range of services, including professional pet grooming, ensuring your pets always look and feel their best with customized grooming packages for all breeds.</p>
            </div>
        </div>
        <div class="row reverse">
            <div class="picture">Picture</div>
            <div class="details">
                <p>In addition to grooming, Pet Lover provides pet boarding services, offering a safe and comfortable environment where your pets can relax while you're away. Our staff ensures that your pets are well-fed, exercised, and given plenty of love and attention during their stay.</p>
            </div>
        </div>
        <div class="row">
            <div class="picture">Picture</div>
            <div class="details">
                <p>For your pet's health, Pet Lover also offers veterinary care with qualified professionals available for routine check-ups, vaccinations, and emergency treatments. We prioritize the well-being of your pets, ensuring they receive top-tier medical attention.</p>
            </div>
        </div>
    </div>
</body>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <footer>
        <div class="footer-container">
            <div class="footer-logo">
                <img src="images/logo.jpg" alt="Pet-Lover-Logo" width=100>
            </div>
            <div class="footer-contact">
                <p>Phone: <a href="tel:017-7505281">017-7505281</a></p>
                <p>Email: <a href="mailto:petlover@gmail.com">petlover@gmail.com</a></p>
                <p>
                    <a href="https://www.facebook.com" target="_blank">Facebook</a> |
                    <a href="https://twitter.com" target="_blank">Twitter</a> |
                    <a href="https://www.instagram.com" target="_blank">Instagram</a>
                </p>
            </div>
            <div class="footer-address">
                <p>
                    <a href="https://www.google.com/maps/place/3,+Jalan+Austin+Heights+Utama,+Taman+Mount+Austin,+81100+Johor+Bahru,+Johor" target="_blank" style="color: black; text-decoration: none;">
                    3, Jalan Austin Heights Utama, Taman Mount Austin, 81100 Johor Bahru, Johor
                    </a>
                </p>
            </div>
        </div>
        <div class="footer-copyright">
            <p>COPYRIGHT &copy; 2024 PET LOVER</p>
        </div>
    </footer>

</body>
</html>
