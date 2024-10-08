<?php
include 'config.php';
session_start();


$customerID = $_SESSION['ID'];

if ($customerID) {

    $stmt = $conn->prepare("SELECT PetID, PetName, CustomerName FROM pets JOIN customer ON pets.CustomerID = customer.CustomerID WHERE pets.CustomerID = ?");
    if (!$stmt) {
        echo "Failed to prepare statement: " . $conn->error;
        exit();
    }

    $stmt->bind_param("i", $customerID);


    if (!$stmt->execute()) {
        echo "Failed to execute query: " . $stmt->error;
        exit();
    }


    $result = $stmt->get_result();
    

    $username = null;
    if ($row = $result->fetch_assoc()) {
        $username = htmlspecialchars($row['CustomerName']);

        $pets = [];
        do {
            $pets[] = ['PetID' => htmlspecialchars($row['PetID']), 'PetName' => htmlspecialchars($row['PetName'])];
        } while ($row = $result->fetch_assoc());
    }
} else {
    $result = null;
    $username = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/booking.css">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"/>
</head>
<body>
    <header>
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


        if ($ServiceID == 1) {
            include 'grooming.html'; 
        } else if ($ServiceID == 2) {
            include 'vetinarycare.html';
        } else if ($ServiceID == 3) {
            include 'petboarding.html';
        } else {
            echo "Invalid Service ID!";
        }
    } else {
        echo "Service ID not provided!";
    }
    ?>
    <div class="floatappoinment">
    <div class="appointment-form">
        <h2>Book an Appointment</h2>
        <form action="confirmation.php" method="post">
            <input type="hidden" name="serviceID" value="<?php echo $ServiceID ?>">  
            <!-- Pet Name Dropdown -->
            <label for="pet-name">Choose Your Pet:</label>
            <select id="pet-name" name="pet-id" required>
                <option value="">Select your pet</option>
                <?php
                if (!empty($pets)) {
                    foreach ($pets as $pet) {
                        echo '<option value="' . $pet['PetID'] . '">' . $pet['PetName'] . '</option>';
                    }
                } else {
                    echo '<option value="">No pets available</option>';
                }
                ?>
            </select>
            <div class="row">
    <div class="item">
      <div class="square white"></div>
      <p>All available</p>
    </div>

    <div class="item">
      <div class="square blue"></div>
      <p>1-2 have been chosen</p>
    </div>
    <div class="item">
      <div class="square orange"></div>
      <p>3-5 have been chosen</p>
    </div>
    <div class="item">
      <div class="square red"></div>
      <p>Full</p>
    </div>
  </div>

            <!-- Preferred Date and Time -->
            <label for="appointment-date">Select Date:</label>
            <input type="text" id="appointment-date" placeholder="Select Date" name="appointment-date" required readonly>
            <br>
            <label for="appointment-time">Select Time:</label>
            <input type="text" id="appointment-time" placeholder="Select Time" name="appointment-time" required readonly>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Book Appointment</button>
        </form>
    </div>
    </div>
    <!-- Include jQuery and DateTimePicker JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

    <script>
        $(document).ready(function() {
            let unavailableDates = []; 
            let lowAvailabilityDates = []; 
            let availableDates = []; 


            $.ajax({
                url: 'check-date.php',
                method: 'GET',
                success: function(response) {
                    let dateStatus = JSON.parse(response);
                    console.log("Date Status Response:", dateStatus);

                    for (let date in dateStatus) {
                        const bookedSlots = parseInt(dateStatus[date], 10);
                        
                        if (bookedSlots >= 6) {
                            unavailableDates.push(date); 
                        } else if (bookedSlots >= 3) {
                            lowAvailabilityDates.push(date); 
                        } else if (bookedSlots >= 0 && bookedSlots <= 2) {
                            availableDates.push(date); 
                        }
                    }


                    let today = new Date();
                    let tomorrow = new Date();
                    tomorrow.setDate(today.getDate() + 1);
                    $('#appointment-date').datetimepicker({
                        format: 'Y-m-d',
                        timepicker: false,
                        minDate: tomorrow,
                        beforeShowDay: function(date) {
                            let formattedDate = date.toISOString().split('T')[0]; 
                            
                            if (unavailableDates.includes(formattedDate)) {
                                return [false, "unavailable-date"]; 
                            }
                            if (lowAvailabilityDates.includes(formattedDate)) {
                                return [true, "low-availability-date"]; 
                            }
                            if (availableDates.includes(formattedDate)) {
                                return [true, "available-date"];
                            }
                            return [true, ""];
                        },
                        onChangeDateTime: function(dp, $input) {
                            handleDateSelection($input.val());
                        }
                    });
                },
                error: function(error) {
                    console.error('Error fetching unavailable dates:', error);
                }
            });

            function handleDateSelection(selectedDate) {

                $.ajax({
                    url: 'check-time.php',
                    method: 'POST',
                    data: { date: selectedDate },
                    success: function(response) {
                        try {
                            let availableTimes = JSON.parse(response); 
                            if (availableTimes.length === 0) {
                                $('#appointment-time').datetimepicker('destroy');
                            } else {
                                $('#appointment-time').datetimepicker('destroy');
                                $('#appointment-time').datetimepicker({
                                    format: 'H:i',
                                    datepicker: false,
                                    allowTimes: availableTimes 
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing JSON response:', e);
                        }
                    },
                    error: function(err) {
                        console.error('Error fetching available times:', err);
                    }
                });
            }
        });
    </script>

    <style>
        .unavailable-date {
            background-color: #dc3545 !important; 
            color: #999999;
            pointer-events: none;
        }
        .low-availability-date {
            background-color: #ffcc66 !important; 
            color: #333333;
        }
        .available-date {
            background-color: #add8e6 !important; 
            color: #333333;
        }
    </style>
</body>
</html>
