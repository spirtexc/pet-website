<?php
include '../config.php';  
session_start(); 

if (!isset($_SESSION['bookingID'])) {
    die('Error: Booking ID is not set in the session.');
}



if (isset($_GET['paymentId']) && isset($_GET['PayerID'])) {
    $paymentID = $_GET['paymentId']; 
    $payerID = $_GET['PayerID'];


    $serviceID = $_SESSION['serviceID'];
    $PaymentMethod = 'paypal';
    $PaymentStatus = 'success';
    $PaymentTime = date('H:i:s');
    $PaymentDate = date('Y-m-d');
    $BookingID = $_SESSION['bookingID'];
    $customerID = $_SESSION['ID'];
    $price = $_SESSION['price'];
    $petName = $_SESSION['petName']; 
    $bookingDate = $_SESSION['bookingDate']; 
    $bookingTime = $_SESSION['bookingTime']; 
    

    $paymentstmt = $conn->prepare("INSERT INTO Payment (PaymentID, BookingID, PaymentDate, PaymentTime, PaymentAmount, PaymentMethod, PayerID, PaymentStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $paymentstmt->bind_param("sissdsss", $paymentID, $BookingID, $PaymentDate, $PaymentTime, $price, $PaymentMethod, $payerID, $PaymentStatus);


    if ($paymentstmt->execute()) {
        $bookingStatus = "Booked"; 
        $bookingstmt = $conn->prepare("UPDATE Bookings SET BookingStatus = ?, paymentStatus = ? WHERE BookingID = ?");
        $bookingstmt->bind_param("ssi", $bookingStatus, $paymentID, $BookingID);

        if ($bookingstmt->execute()) { 

            sendConfirmationEmail($customerID, $price, $BookingID, $petName, $bookingDate, $bookingTime);


            $_SESSION['ID'] = $customerID;


            redirectWithAlert('Booking successful. A confirmation email has been sent.', '../service.php');
        } else {
            echo "Error updating booking: " . $bookingstmt->error;
        }
    } else {
        echo "Error inserting payment: " . $paymentstmt->error;
    }


    $paymentstmt->close();
    $bookingstmt->close();
    $conn->close();
} else {
    echo "PaymentID or PayerID is missing!";
}


function redirectWithAlert($message, $url) {
    $_SESSION['alert'] = $message;
    header("Location: $url");
    exit();
}


function sendConfirmationEmail($customerID, $price, $BookingID, $petName, $bookingDate, $bookingTime) {
    global $conn; 


    $stmt = $conn->prepare("SELECT Email, customerName FROM customer WHERE customerID = ?");
    $stmt->bind_param("i", $customerID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $email = $data['Email'];
        $customerName = $data['customerName'];


        $mail = require dirname(__FILE__) . '/../mailer.php';       


        if ($mail instanceof PHPMailer\PHPMailer\PHPMailer) {
            $mail->setFrom("jackkee3123@gmail.com", 'Payment Confirmation');
            $mail->addAddress($email); 
            $mail->Subject = "Booking Confirmation and Payment Successful";

            $mail->isHTML(true);
            $mail->Body = '
            <p style="font-size: 16px; color: black;">Dear ' . htmlspecialchars($customerName) . ',</p>
            <p style="font-size: 14px; color: black;">Thank you for your payment. We are pleased to inform you that your transaction was successful.</p>
            <p style="font-size: 14px; color: black;">Booking Details for Booking ID: ' . htmlspecialchars($BookingID) . '</p>
            <p style="font-size: 14px; color: black;">Pet Name: ' . htmlspecialchars($petName) . '</p>
            <p style="font-size: 14px; color: black;">Amount Paid: RM ' . number_format($price, 2) . '</p>
            <p style="font-size: 14px; color: black;">Date of Booking: ' . htmlspecialchars($bookingDate) . '</p>
            <p style="font-size: 14px; color: black;">Time of Booking: ' . htmlspecialchars($bookingTime) . '</p>
            <p style="font-size: 14px; color: black;">Should you have any questions or require further assistance, please do not hesitate to contact us.</p>
            <p style="font-size: 14px; color: black;">Best regards,<br><strong>PET LOVER</strong><br>017-750-5281</p>
        ';
        


            try {
                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
            }
        }
    }
}
?>
