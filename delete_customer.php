<?php
include 'config.php';

if (isset($_GET['id'])) {
    $customerID = intval($_GET['id']);

    // SQL query to delete the customer
    $deleteQuery = "DELETE FROM customer WHERE CustomerID = $customerID";

    if (mysqli_query($conn, $deleteQuery)) {
        echo "Customer deleted successfully.";
    } else {
        echo "Error deleting customer: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    header("Location: admin.php"); // Redirect back to admin dashboard
}
?>
