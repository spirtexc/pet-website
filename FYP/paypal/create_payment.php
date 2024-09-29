<?php
include '../config.php';
session_start();
$customerID= $_SESSION['ID'];



if (isset($_SESSION['bookingID'])){
    $query = "SELECT b.bookingID, s.ServiceName, s.Price, s.ServiceID 
          FROM Bookings b 
          JOIN service s ON b.ServiceID = s.ServiceID 
          WHERE b.BookingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i',$_SESSION['bookingID']);
    if ($stmt->execute()) {
        // Get the result
        $result = $stmt->get_result();
        // Check if any rows were returned
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $serviceName = $row['ServiceName'];
            $serviceID = $row['ServiceID'];
            $price = $row['Price'];
            
            //pass into session
            $_SESSION['serviceName'] = $serviceName;
            $_SESSION['serviceID'] = $serviceID;
            $_SESSION['price'] = $price;
     
            // PayPal API credentials
            $clientId = 'Adc5N9EdeH8tGZ9kFDLSi6OZ7mzsT97Y3UWFQdb3h_j0muPSvtnYiZ_gK--PWb2O3Jc2l4B3uBMqrcwJ';
            $secret = 'EGsG7cLrJChj_g1iQxayRnUJqeiaRAmkajiTQhkg0frglCP0W2Tsa4ZFSrKtXeQCE1UFrXgfZoIuEeZO';

            // Get access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Accept: application/json",
                "Accept-Language: en_US"
            ]);
            curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/ssl/cacert.pem'); // Add this line

            $response = curl_exec($ch);
            if ($response === false) {
                die('Error getting access token: ' . curl_error($ch));
            }

            $accessToken = json_decode($response);
            if (!isset($accessToken->access_token)) {
                die('Error: ' . $response);
            }
            $accessToken = $accessToken->access_token;
            curl_close($ch);

            // Create payment

            $projectRoot = dirname(__DIR__, 2); // Move up two directories to the project root
            $return_url = "http://localhost/" . basename($projectRoot) . "/FYP/paypal/success.php";
            $cancel_url = "http://localhost/" . basename($projectRoot) . "/FYP/paypal/cancel.php";
            $paymentDescription = "Payment". $serviceName;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer $accessToken"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "intent" => "sale",
                "redirect_urls" => [
                    "return_url" => $return_url,
                    "cancel_url" => $cancel_url
                ],
                "payer" => [
                    "payment_method" => "paypal"
                ],
                "transactions" => [[
                    "amount" => [
                        "total" => $price,
                        "currency" => "MYR"
                    ],
                    "description" => $paymentDescription
                ]]
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/ssl/cacert.pem'); // Add this line

            $response = curl_exec($ch);
            if ($response === false) {
                die('Error creating payment: ' . curl_error($ch));
            }

            $payment = json_decode($response);
            if (!isset($payment->links)) {
                die('Error: ' . $response);
            }
            curl_close($ch);

            // Redirect user to PayPal for approval
            foreach ($payment->links as $link) {
                if ($link->rel == 'approval_url') {
                    header("Location: $link->href");
                    exit();
                }
            }
        } else {
            echo "Booking not found.";
        }
    } else {
        echo "Error executing query.";
    }
}

?>
