<?php
include 'config.php'; // Include your database connection file
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the POST request
    $petID = $_POST['petID'];
    $serviceID = $_POST['serviceID'];
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];
    $bookingStatus = 'On Hold';
    $paymentStatus = 'Pending';

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO Bookings (PetID, ServiceID, BookingDate, BookingTime, BookingStatus, paymentStatus) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $petID, $serviceID, $appointmentDate, $appointmentTime, $bookingStatus, $paymentStatus);

    if ($stmt->execute()) {
        $_SESSION['bookingID'] = $conn->insert_id;
        echo "<script>
         alert('Your booking has been created successfully. Booking ID: " . $_SESSION['bookingID'] . "');
        window.location.href = './paypal/create_payment.php';
    </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
