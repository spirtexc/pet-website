<?php
include '../config.php';  // Database connection
session_start();  // start session 
if (!isset($_SESSION['bookingID'])) {
    die('Error: Booking ID is not set in the session.');
}
// Check if PayPal paymentId and payerID are present
if (isset($_GET['paymentId']) && isset($_GET['PayerID'])) {
    $paymentID = $_GET['paymentId']; 
    $payerID = $_GET['PayerID'];
    // Get the session data
        $serviceID = $_SESSION['serviceID'];
        //Set default payment details
        $PaymentMethod = 'paypal';
        $PaymentStatus = 'success';
        $PaymentTime = date('H:i:s'); // Current time for payment
        $PaymentDate = date('Y-m-d'); // Current date for payment
        $BookingID =  $_SESSION['bookingID'];  // Get the newly created Booking ID
        $customerID = $_SESSION['ID'];
        $serviceID = $_SESSION['serviceID'];
        $price=$_SESSION['price'];
            // Prepare and bind the SQL statement for payment
            $paymentstmt = $conn->prepare("INSERT INTO Payment (PaymentID, BookingID, PaymentDate, PaymentTime, PaymentAmount, PaymentMethod, PayerID, PaymentStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $paymentstmt->bind_param("sissdsss", $paymentID, $BookingID, $PaymentDate, $PaymentTime, $price, $PaymentMethod, $payerID, $PaymentStatus);

            // Execute the payment insertion
            if ($paymentstmt->execute()) {
                $BookingID = $_SESSION['bookingID'];
                $bookingStatus = "Booked"; 
                $bookingstmt = $conn->prepare("UPDATE Bookings SET BookingStatus = ?, paymentStatus = ? WHERE BookingID = ?");
                $bookingstmt->bind_param("ssi", $bookingStatus, $paymentID, $BookingID);
                if ($bookingstmt->execute()) { 
                // Store the session ID in a temporary variable
                $customerID = $_SESSION['ID'];
                // Unset all session variables except for the customer ID
                session_unset();
                $_SESSION['ID'] = $customerID;
                // Success message and redirect
                redirectWithAlert('Booking successful. A confirmation email has been sent.', '../service.php');
                }else{
                    echo "Error inserting booking: " . $bookingstmt->error;
                }
            } else {
                // Payment insertion error
                echo "Error inserting payment: " . $paymentstmt->error;
            }
        // Close the statements and connection
        $stmt->close();
        $paymentstmt->close();
        $conn->close();
} else {
    echo "PaymentID or PayerID is missing!";
}

// Function to display an alert and redirect
function redirectWithAlert($message, $url) {
    echo "<script type='text/javascript'>
            alert('$message');
            window.location.href='$url';
          </script>";
}
?>
