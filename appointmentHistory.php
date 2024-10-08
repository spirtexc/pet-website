<?php
include 'config.php';
session_start();

// Fetch customer name
$username = '';
if (isset($_SESSION['ID'])) {
    $customerID = $_SESSION['ID'];
    $query = "SELECT CustomerName FROM Customer WHERE CustomerID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $customerID);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
}

// Initialize filter variables
$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
$filterColumn = isset($_POST['filterColumn']) ? $_POST['filterColumn'] : '';

// Start building the SQL query
$filtersql = "
    SELECT 
    b.bookingID, 
    COALESCE(pet.PetName, history.PetName) AS PetName, 
    s.ServiceName, 
    b.bookingDate, 
    b.bookingTime, 
    b.BookingStatus, 
    b.PaymentStatus,  
    c.CustomerName 
    FROM bookings b
    LEFT JOIN Pets pet ON b.PetID = pet.PetID 
    LEFT JOIN pet_history history ON b.PetID = history.PetID  
    JOIN service s ON b.ServiceID = s.ServiceID 
    JOIN Customer c ON pet.CustomerID = c.CustomerID
    LEFT JOIN payment pay ON b.BookingID = pay.BookingID 
    WHERE c.CustomerID = ?";



// Append search filter if input is provided
if (!empty($filterColumn) && !empty($searchTerm)) {
    $allowedColumns = ['BookingID', 'PetName', 'ServiceName', 'BookingDate', 'BookingTime', 'BookingStatus', 'PaymentStatus'];

    // Check if the selected filter column is valid
    if (in_array($filterColumn, $allowedColumns)) {
        if ($filterColumn === 'PetName') {
            $filtersql .= " AND pet.PetName LIKE ?";
        } elseif ($filterColumn === 'ServiceName') {
            $filtersql .= " AND s.ServiceName LIKE ?";
        } else {
            $filtersql .= " AND b.$filterColumn LIKE ?";
        }
    }
}

// Prepare the SQL statement
$filterstmt = $conn->prepare($filtersql);

// Bind parameters depending on whether a filter is applied
if (!empty($filterColumn) && !empty($searchTerm)) {
    $searchTermWildcard = '%' . $searchTerm . '%'; // Wildcard search term for LIKE query
    $filterstmt->bind_param('is', $customerID, $searchTermWildcard); // Bind customerID and search term
} else {
    $filterstmt->bind_param('i', $customerID); // Only bind customerID if no filter is applied
}

// Execute the query
$filterstmt->execute();
$result = $filterstmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/appointmenthistory.css">
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
        <h1>Booking List</h1>
        <!-- Filtering form -->
        <form method='POST' action=''>
            <label for='searchTerm'>Search:</label>
            <input type='text' id='searchTerm' name='searchTerm' placeholder='Enter keyword' value='<?php echo htmlspecialchars($searchTerm); ?>'>

            <label for='filterColumn'>Filter by:</label>
            <select id='filterColumn' name='filterColumn'>
                <option value=''>Select Column</option>
                <option value='BookingID' <?php echo ($filterColumn === 'BookingID' ? 'selected' : ''); ?>>Booking ID</option>
                <option value='PetName' <?php echo ($filterColumn === 'PetName' ? 'selected' : ''); ?>>Pet Name</option>
                <option value='ServiceName' <?php echo ($filterColumn === 'ServiceName' ? 'selected' : ''); ?>>Service Name</option>
                <option value='BookingDate' <?php echo ($filterColumn === 'BookingDate' ? 'selected' : ''); ?>>Booking Date</option>
                <option value='BookingTime' <?php echo ($filterColumn === 'BookingTime' ? 'selected' : ''); ?>>Booking Time</option>
                <option value='BookingStatus' <?php echo ($filterColumn === 'BookingStatus' ? 'selected' : ''); ?>>Booking Status</option>
                <option value='PaymentStatus' <?php echo ($filterColumn === 'PaymentStatus' ? 'selected' : ''); ?>>Payment Status</option>
            </select>

            <input type='submit' value='Filter'>
        </form>

        <?php
        // Check if there are any results
        if ($result && $result->num_rows > 0) {
            // Display table
            echo "<table class='booking-table'>
                    <tr>
                        <th>Booking ID</th>
                        <th>Pet Name</th>
                        <th>Service Name</th>
                        <th>Booking Date</th>
                        <th>Booking Time</th>
                        <th>Booking Status</th>
                        <th>Payment Status</th>
                        <th>Cancel</th>
                        <th>Update</th>";

            if (isset($_SESSION['ID'])) {
                echo "<th>Make Payment</th>";
            }

            echo "</tr>";

            // Output data for each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row["bookingID"] ?? '') . "</td>
                    <td>" . htmlspecialchars($row["PetName"] ?? '') . "</td>
                    <td>" . htmlspecialchars($row["ServiceName"] ?? '') . "</td>
                    <td>" . htmlspecialchars($row["bookingDate"] ?? '') . "</td>
                    <td>" . htmlspecialchars($row["bookingTime"] ?? '') . "</td>
                    <td>" . htmlspecialchars($row["BookingStatus"] ?? '') . "</td>
                    <td>" . htmlspecialchars($row["PaymentStatus"] ?? '') . "</td>";
                
          
            
                // Display Cancel and Update buttons
                echo "<td>
                        <form action='cancel_booking.php' method='post'>
                            <input type='hidden' name='bookingID' value='" . htmlspecialchars($row["bookingID"]) . "'>
                            <input type='submit' value='Cancel'>
                        </form>
                      </td>
                      <td>
                        <form action='update_booking.php' method='get'>
                            <input type='hidden' name='bookingID' value='" . htmlspecialchars($row["bookingID"]) . "'>
                            <input type='submit' value='Update'>
                        </form>
                      </td>";
            
                
            

                // Check for Payment status
                if (isset($_SESSION['ID'])) {
                    if ($row["PaymentStatus"] === 'Pending') {
                        echo "<td>    
                                <form action='paypal/create_payment.php' method='post'>
                                    <input type='hidden' name='bookingID' value='" . htmlspecialchars($row["bookingID"]) . "'>
                                    <input type='submit' value='Make Payment'>
                                </form>
                              </td>";

                    } else {
                        echo "<td>Paid</td>";
                    }
                } else {
                    echo "<td></td>";
                }

                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No bookings found.</p>";
        }

        // Close the connection
        $conn->close();
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
