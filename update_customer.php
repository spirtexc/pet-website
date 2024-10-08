<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['CustomerID'];
    $customerName = $_POST['CustomerName'];
    $email = $_POST['Email'];
    $phoneNumber = $_POST['PhoneNumber'];
    $newPassword = $_POST['Password'];
    $updateQuery = "
        UPDATE customer
        SET CustomerName = '$customerName', Email = '$email', PhoneNumber = '$phoneNumber'
        ";

    if (isset($newPassword) && $newPassword !== '***'){
        $hashPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateQuery .= ", Password = $hashPassword";
    }

    $updateQuery .= " WHERE CustomerID = $customerId";
    
    if ($conn->query($updateQuery) === TRUE) {
        echo "Customer updated successfully.";
    } else {
        echo "Error updating customer: " . $conn->error;
    }
}

$conn->close();
?>
