<?php
include 'config.php';  
session_start(); 
if (isset($_POST['bookingID'])) {
    $query = "SELECT b.bookingID, s.ServiceName, s.Price, s.ServiceID, p.PetName, b.BookingDate, b.BookingTime, p.CustomerID
              FROM Bookings b 
              JOIN service s ON b.ServiceID = s.ServiceID 
              join pets p on b.PetID = p.PetID
              WHERE b.BookingID = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $_POST['bookingID']);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $price = $row['Price'];
            $petName = $row['PetName'];
            $CustomerID = $row['CustomerID'];
            $bookingDate = $row['BookingDate'];
            $bookingTime = $row['BookingTime'];
            $PaymentTime = date('H:i:s');
            $PaymentDate = date('Y-m-d');
            $paymentNum = random_int(100000000,900000000);
            $paymentID = 'CASH-' . $paymentNum;
            $payerID = 'Admin';
            $PaymentMethod = 'Cash';
            $PaymentStatus = 'success';
            $paymentstmt = $conn->prepare("INSERT INTO Payment (PaymentID, BookingID, PaymentDate, PaymentTime, PaymentAmount, PaymentMethod, PayerID, PaymentStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $paymentstmt->bind_param("sissdsss", $paymentID, $_POST['bookingID'], $PaymentDate, $PaymentTime, $price, $PaymentMethod, $payerID, $PaymentStatus);
            if ($paymentstmt->execute()) {
                $bookingStatus = "Booked"; 
                $bookingstmt = $conn->prepare("UPDATE Bookings SET BookingStatus = ?, paymentStatus = ? WHERE BookingID = ?");
                $bookingstmt->bind_param("ssi", $bookingStatus, $paymentID, $_POST['bookingID']);
        
                if ($bookingstmt->execute()) { 
                    sendConfirmationEmail($CustomerID, $price, $_POST['bookingID'], $petName, $bookingDate, $bookingTime);
                    redirectWithAlert('Payment successful. A confirmation email has been sent.', './admin.php');
                } else {
                    echo "Error updating booking: " . $bookingstmt->error;
                }
            } else {
                echo "Error inserting payment: " . $paymentstmt->error;
            }
        }
    }
}
    $paymentstmt->close();
    $bookingstmt->close();
    $conn->close();

    function sendConfirmationEmail($CustomerID, $price, $BookingID, $petName, $bookingDate, $bookingTime) {
        global $conn; 
    
    
        $stmt = $conn->prepare("SELECT Email, customerName FROM customer WHERE customerID = ?");
        $stmt->bind_param("i", $CustomerID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $email = $data['Email'];
            $customerName = $data['customerName'];
    
    
            $mail = require dirname(__FILE__) . '/mailer.php';       
    
    
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
    function redirectWithAlert($message, $url) {
        $_SESSION['alert'] = $message;
        header("Location: $url");
        exit();
    }
    
?>