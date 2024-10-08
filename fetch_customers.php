<?php
include 'config.php'; // Database connection

// Fetch customers
$customerQuery = "SELECT CustomerID, CustomerName, Email, PhoneNumber FROM customer";
$result = mysqli_query($conn, $customerQuery);

// Check if there are customers and display them
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['CustomerID']}</td>
                <td>{$row['CustomerName']}</td>
                <td>{$row['Email']}</td>
                <td>{$row['PhoneNumber']}</td>
                <td>
                    <a href='view_customer.php?id={$row['CustomerID']}' class='btn btn-info btn-sm'>View</a>
                    <a href='edit_customer.php?id={$row['CustomerID']}' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='delete_customer.php?id={$row['CustomerID']}' class='btn btn-danger btn-sm'>Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No customers found.</td></tr>";
}

mysqli_close($conn);
?>
