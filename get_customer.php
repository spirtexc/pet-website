<?php
include 'config.php';

if (isset($_GET['CustomerID'])) {
    $customerId = $_GET['CustomerID'];

    $query = "SELECT * FROM customer WHERE CustomerID = $customerId";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        echo json_encode($customer);
    } else {
        echo json_encode(['error' => 'Customer not found']);
    }
}

$conn->close();
?>
