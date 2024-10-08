<?php
include 'config.php'; // Ensure your database connection is set here

// Retrieve filter values from POST
$bookingStatus = isset($_POST['bookingStatus']) ? $_POST['bookingStatus'] : '';
$paymentStatus = isset($_POST['paymentStatus']) ? $_POST['paymentStatus'] : '';
$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';

// SQL query to retrieve appointment data with filters
$filtersql = "
    SELECT 
        b.bookingID, 
        COALESCE(pet.PetName, history.PetName) AS PetName, 
        s.ServiceName, 
        b.bookingDate, 
        b.bookingTime, 
        b.BookingStatus, 
        b.PaymentStatus
    FROM bookings b
    LEFT JOIN Pets pet ON b.PetID = pet.PetID 
    LEFT JOIN pet_history history ON b.PetID = history.PetID 
    JOIN service s ON b.ServiceID = s.ServiceID 
";

// Check if filter parameters are set and modify the query accordingly
$filterConditions = [];

if ($bookingStatus !== '') {
    $filterConditions[] = "b.BookingStatus = '" . mysqli_real_escape_string($conn, $bookingStatus) . "'";
}
if ($paymentStatus == 'Pending' && $paymentStatus !== '') {
    $filterConditions[] = "b.PaymentStatus = '" . mysqli_real_escape_string($conn, $paymentStatus) . "'";
}elseif ($paymentStatus == 'Paid' && $paymentStatus !== '') {
    $filterConditions[] = "b.PaymentStatus LIKE 'PAYID-%'";
}

if ($searchTerm !== '') {
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $filterConditions[] = "(b.bookingID LIKE '%$searchTerm%' OR pet.PetName LIKE '%$searchTerm%' OR s.ServiceName LIKE '%$searchTerm%')";
}

if (count($filterConditions) > 0) {
    $filtersql .= " WHERE " . implode(" AND ", $filterConditions);
}

$result = mysqli_query($conn, $filtersql);

// Check for SQL errors
if (!$result) {
    die('Error executing query: ' . mysqli_error($conn));
}

// Check if there are results and display them
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['bookingID']}</td>
                <td>{$row['PetName']}</td>
                <td>{$row['ServiceName']}</td>
                <td>{$row['bookingDate']}</td>
                <td>{$row['bookingTime']}</td>
                <td>{$row['BookingStatus']}</td>
                <td>{$row['PaymentStatus']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No appointments found.</td></tr>";
}

mysqli_close($conn); // Close the connection
?>
