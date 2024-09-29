<?php
// Include database configuration
include 'config.php';
session_start();

// Clear any previous error message
unset($_SESSION['error_message']);

// Get POST data and sanitize input
$username = $_POST['username'];
$userpassword =$_POST['userpassword']; // Plain text password

// Check if the user is an admin (optional admin check)
if ($username === 'admin' && $userpassword === '12345') {
    header('Location: admin.php');
    $_SESSION["ID"] = $customerID;
    exit();
} else {
    // Prepare a statement to prevent SQL injection
    if ($stmt = $conn->prepare("SELECT CustomerID, Password FROM Customer WHERE CustomerName = ?")) {
        $stmt->bind_param("s", $username);

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedPassword = $row['Password']; // Plain text password
            $customerID = $row['CustomerID'];
            
            // Verify the password (plain text comparison)
            if (password_verify($userpassword, $storedPassword)) {
                // Set session variables
                $_SESSION["ID"] = $customerID; // Store CustomerID in session
                
                header('Location: service.php'); // Redirect to user page
                exit();
            } else {
                // Store error message in session
                $_SESSION['error_message'] = 'Invalid username or password.';
                header('Location: Homepage.php?error=1');
                exit();
            }
        } else {
            // Store error message in session
            $_SESSION['error_message'] = 'Invalid username or password.';
            header('Location: Homepage.php?error=1');
            exit();
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing the SQL statement.";
    }
}

// Close the connection
$conn->close();
?>