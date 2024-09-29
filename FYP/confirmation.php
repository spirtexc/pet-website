<?php 
include "config.php";
session_start();
$customerID = $_SESSION['ID'];
$appointmentDate = $_POST['appointment-date'];
$appointmentTime = $_POST['appointment-time'];
$petID = $_POST['pet-id'];
$serviceID = $_POST['serviceID'];

// Prepare and execute the combined query
$query = "SELECT s.serviceName, s.price, c.customerName, p.petName FROM service s 
JOIN customer c ON c.customerID = ? 
JOIN pets p ON p.petid = ? 
WHERE s.serviceid = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $customerID, $petID, $serviceID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $serviceName = $data['serviceName'];
    $price = $data['price'];
    $customerName = $data['customerName'];
    $petName = $data['petName'];
} else {
    // Handle error: No results found
    die("No results found.");
}

// Close the prepared statement
$stmt->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Page</title>
    <link rel="stylesheet" href="css/confirmation.css">
</head>
<body>
    <div class="container">
        <h1 class="confirmation-title">Order Confirmation</h1>
        <div class="description">
            <p>Thank you for your order! Please review your order details below.</p>
        </div>


        
        <form action="insert_booking.php" method="post">
       

            <label name="customerName" class="customerName">customer Name: <?php echo $customerName ?></label>
            <br>
            <label name="petName" class="petName">pet Name: <?php echo $petName ?></label>
            <input type="hidden" name="petID" value="<?php echo htmlspecialchars($petID); ?>">
            <br>
            <label name="serviceName" class="serviceName">service ID: <?php echo $serviceName ?></label>
            <input type="hidden" name="serviceID" value="<?php echo $serviceID; ?>">
            <br>
            <label name="appointmentDate" class="appointmentDate">appointment Date: <?php echo $appointmentDate ?></label>
            <input type="hidden" name="appointmentDate" value="<?php echo $appointmentDate; ?>">
            <br>
            <label name="appointmentTime" class="appointmentTime">appointment Time: <?php echo $appointmentTime ?> </label>
            <input type="hidden" name="appointmentTime" value="<?php echo $appointmentTime; ?>">
            <br>
            <label class="price">Price: RM<?php echo number_format($price, 2); ?></label>
            <div class="payment-section">
                <label for="paymentType" class="payment-label">Select Payment Type:</label>
                <select id="paymentType" class="payment-dropdown">
                    <option value="credit-card">Credit Card</option>
                    <option value="debit-card">Debit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank-transfer">Bank Transfer</option>
                </select>
            </div>
            <button class="payment-button" onclick=checkPaymentType();>Proceed to Payment</button>
        </form>
        
        
        
    </div>
    <script>
        function checkPaymentType() {
            var paymentType = document.getElementById("paymentType").value;
            
            if (paymentType === "paypal") {
                // Redirect to create_payment.php in the PayPal directory
                window.location.href = 'insert_booking.php';
            } else if (paymentType === "") {
                alert("Payment Type not selected");
            } else {
                alert("Selected Payment Type: " + paymentType);
                // You can handle other payment types here if needed
            }
        }
     
    
    </script>
</body>
</html>
