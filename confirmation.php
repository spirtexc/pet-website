<?php 
include "config.php";
session_start();
$customerID = $_SESSION['ID'];
$appointmentDate = $_POST['appointment-date'];
$appointmentTime = $_POST['appointment-time'];
$petID = $_POST['pet-id'];
$serviceID = $_POST['serviceID'];

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

    die("No results found.");
}

$stmt->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Page</title>
    <link rel="stylesheet" href="css/confirmations.css">
</head>
<body>
    <div class="container">
        <h1 class="confirmation-title">Order Confirmation</h1>
        <div class="description">
            <p>Thank you for your order! Please review your order details below.</p>
        </div>


        
        <form action="insert_booking.php" method="post">
       
        <table class="order-table">
                <tr>
                    <th>Customer Name</th>
                    <td>
                        <?php echo htmlspecialchars($customerName); ?>
                        <input type="hidden" name="customerName" value="<?php echo htmlspecialchars($customerName); ?>">
                    </td>
                </tr>
                <tr>
                    <th>Pet Name</th>
                    <td>
                        <?php echo htmlspecialchars($petName); ?>
                        <input type="hidden" name="petID" value="<?php echo htmlspecialchars($petID); ?>">
                    </td>
                </tr>
                <tr>
                    <th>Service Name</th>
                    <td>
                        <?php echo htmlspecialchars($serviceName); ?>
                        <input type="hidden" name="serviceID" value="<?php echo htmlspecialchars($serviceID); ?>">
                    </td>
                </tr>
                <tr>
                    <th>Appointment Date</th>
                    <td>
                        <?php echo htmlspecialchars($appointmentDate); ?>
                        <input type="hidden" name="appointmentDate" value="<?php echo htmlspecialchars($appointmentDate); ?>">
                    </td>
                </tr>
                <tr>
                    <th>Appointment Time</th>
                    <td>
                        <?php echo htmlspecialchars($appointmentTime); ?>
                        <input type="hidden" name="appointmentTime" value="<?php echo htmlspecialchars($appointmentTime); ?>">
                    </td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>RM<?php echo number_format($price, 2); ?></td>
                </tr>
            </table>
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
        <button class="return-button" onclick="window.history.back();">Return</button>

        
        
    </div>
    <script>
        function checkPaymentType() {
            var paymentType = document.getElementById("paymentType").value;
            
            if (paymentType === "paypal") {
                window.location.href = 'insert_booking.php';
            } else if (paymentType === "") {
                alert("Payment Type not selected");
            } else {
                alert("Selected Payment Type: " + paymentType);
            }
        }
     
    
    </script>
</body>
</html>
