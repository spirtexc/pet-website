<?php
include 'config.php';

// Query to get the count of booked time slots for each date
$sql = "SELECT BookingDate, COUNT(*) as bookedSlots FROM Bookings GROUP BY BookingDate";
$result = $conn->query($sql);

// Initialize an array to store the date status
$dateStatus = [];

while ($row = $result->fetch_assoc()) {
    $dateStatus[$row['BookingDate']] = $row['bookedSlots'];
}

echo json_encode($dateStatus);

$conn->close();
?>
