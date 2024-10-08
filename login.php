<?php

include 'config.php';
session_start();


unset($_SESSION['error_message']);


$username = $_POST['username'];
$userpassword =$_POST['userpassword']; 
// Check if the user is an admin (optional admin check)
if ($username === 'admin' && $userpassword === '12345') {
    header('Location: admin.php');
    $_SESSION["ID"] = $customerID;
    exit();
} else {

    if ($stmt = $conn->prepare("SELECT CustomerID, Password FROM Customer WHERE CustomerName = ?")) {
        $stmt->bind_param("s", $username);


        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedPassword = $row['Password']; 
            $customerID = $row['CustomerID'];
            

            if (password_verify($userpassword, $storedPassword)) {

                $_SESSION["ID"] = $customerID; 
                
                
                header('Location: Homepage.php');
                
                exit();
            } else {

                $_SESSION['error_message'] = 'Invalid username or password.';
                header('Location: index.php?error=1');
                exit();
            }
        } else {

            $_SESSION['error_message'] = 'Invalid username or password.';
            header('Location: index.php?error=1');
            exit();
        }


        $stmt->close();
    } else {
        echo "Error preparing the SQL statement.";
    }
}


$conn->close();
?>