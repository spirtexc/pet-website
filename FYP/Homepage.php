<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
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
                            <a class="dropdown-item" href="serviceDetail.php?serviceID=1">Grooming</a>
                            <a class="dropdown-item" href="serviceDetail.php?serviceID=2">Veterinary Care</a>
                            <a class="dropdown-item" href="serviceDetail.php?serviceID=3">Pet Boarding</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Review</a>
                    </li>
                </ul>
                <button class="btnLogin-popup">Login</button>
            </div>
        </nav>
    </header>
    <!-- delete after homepage done -->
    <img src="FYP\images\homepage_example.PNG" alt="Profile" class="profile-icon">
    <div class="wrapper">
        <span class="icon-close"><ion-icon name="close-outline"></ion-icon></span>

        <!-- Login Form -->
        <div class="form-box login">
            <h2>Login</h2>
            <?php
    // Display error message if it exists
    if (isset($_SESSION['error_message'])) {
        echo "<div class='login-error-box'>" . $_SESSION['error_message'] . "</div>";
        unset($_SESSION['error_message']); 
    }
    ?>
            <form action="login.php" method="POST">
                <div class="input-boxlogin">
                    <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-boxlogin">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name="userpassword" required>
                    <label>Password</label>
                </div>
                <div class="remember-forgot">
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="login-register">
                    <p>Don't have an account?<a href="#" class="register-link">Register</a></p>
                </div>
            </form>
        </div>

        <!-- Register Form -->
        <div class="form-box register">
        <h2 class="registration-title">Registration</h2>
        <?php
    // Display error message if it exists
    if (isset($_SESSION['registererror_message'])) {
        echo "<div class='register-error-box'>" . $_SESSION['registererror_message'] . "</div>";
        unset($_SESSION['registererror_message']); 
    }
    ?>
            <form action="register.php" method="POST">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="call-outline"></ion-icon></span>
                    <input type="tel" name="phone" required>
                    <label>Phone Number</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name="userpassword" required>
                    <label>Password</label>
                </div>
                
                <!-- Add Pet Button and Details -->
                <div class="additional-info-container">
                    <div class="pet-section">
                        <button type="button" onclick="addPet()">Add Pet Detail +</button>
                        <div id="petDetails"></div>
                    </div>
                </div>
                <button type="submit" class="btn">Register</button>
                <div class="login-register">
                    <p>Already have an account?<a href="#" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Pet Details Script -->
    <script>
        let petIndex = 0;

        function addPet() {
            petIndex++;

            let collapsible = document.createElement('button');
            collapsible.className = 'collapsible';
            collapsible.innerText = `Pet ${petIndex}`;
            collapsible.setAttribute('type', 'button');

            let contentDiv = document.createElement('div');
            contentDiv.className = 'content';

            let nameInput = document.createElement('input');
            nameInput.setAttribute('type', 'text');
            nameInput.setAttribute('name', `pets[${petIndex}][name]`);
            nameInput.setAttribute('placeholder', 'Pet Name');
            nameInput.required = true;

            let speciesInput = document.createElement('input');
            speciesInput.setAttribute('type', 'text');
            speciesInput.setAttribute('name', `pets[${petIndex}][species]`);
            speciesInput.setAttribute('placeholder', 'Species');
            speciesInput.required = true;

            let dobInput = document.createElement('input');
            dobInput.setAttribute('type', 'date');
            dobInput.setAttribute('name', `pets[${petIndex}][dob]`);
            dobInput.required = true;

            let weightInput = document.createElement('input');
            weightInput.setAttribute('type', 'number');
            weightInput.setAttribute('step', '0.01');
            weightInput.setAttribute('name', `pets[${petIndex}][weight]`);
            weightInput.setAttribute('placeholder', 'Weight');
            weightInput.required = true;

            let allergiesInput = document.createElement('input');
            allergiesInput.setAttribute('type', 'text');
            allergiesInput.setAttribute('name', `pets[${petIndex}][allergies]`);
            allergiesInput.setAttribute('placeholder', 'Allergies (optional)');

            let deleteButton = document.createElement('button');
            deleteButton.setAttribute('type', 'button');
            deleteButton.innerText = 'Delete Pet';
            deleteButton.onclick = function() {
                petDetails.removeChild(collapsible);
                petDetails.removeChild(contentDiv);
            };

            contentDiv.appendChild(nameInput);
            contentDiv.appendChild(speciesInput);
            contentDiv.appendChild(dobInput);
            contentDiv.appendChild(weightInput);
            contentDiv.appendChild(allergiesInput);
            contentDiv.appendChild(deleteButton);

            document.getElementById('petDetails').appendChild(collapsible);
            document.getElementById('petDetails').appendChild(contentDiv);

            collapsible.addEventListener('click', function() {
                this.classList.toggle('active');
                let content = this.nextElementSibling;
                if (content.style.display === 'block') {
                    content.style.display = 'none';
                } else {
                    content.style.display = 'block';
                }
            });
        }
       
        function checkError() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');

    if (error) {
        if (error === '1') {
            // Open the login popup if error 1 occurs (login error)
            document.querySelector('.btnLogin-popup').click(); 
        } else if (error === '2') {
            // Open the login popup first for error 2 (registration error)
            document.querySelector('.btnLogin-popup').click(); 
            // Immediately click the register link
            document.querySelector('.register-link').click();
        }
    }
}


    </script>

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
