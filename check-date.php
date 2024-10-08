<?php
include 'config.php';


$sql = "SELECT BookingDate, COUNT(*) as bookedSlots FROM Bookings GROUP BY BookingDate";
$result = $conn->query($sql);


$dateStatus = [];

while ($row = $result->fetch_assoc()) {
    $dateStatus[$row['BookingDate']] = $row['bookedSlots'];
}

echo json_encode($dateStatus);

$conn->close();
?>
