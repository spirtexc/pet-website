<?php
include 'config.php';

if (isset($_POST['bookingID'])) {
    $bookingID = $_POST['bookingID'];

    // Fetch booking details from the database
    $query = "SELECT b.bookingID, p.PetName, s.ServiceName, b.bookingDate, b.bookingTime, b.BookingStatus 
              FROM bookings b
              JOIN Pets p ON b.PetID = p.PetID
              JOIN service s ON b.ServiceID = s.ServiceID
              WHERE b.bookingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $bookingDetails = $result->fetch_assoc();
        echo json_encode($bookingDetails);
    } else {
        echo json_encode([]);
    }
}
?>
