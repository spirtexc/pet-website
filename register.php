<?php
// Include database configuration
include 'config.php';
session_start();

// Get POST data
$customerName = $_POST['username'];
$email = $_POST['email'];
$phoneNumber = $_POST['phone'];
$userpassword = $_POST['userpassword'];
$hashedpassword = password_hash($userpassword, PASSWORD_DEFAULT);
// Check if username or email already exists
$sql_check = "SELECT * FROM Customer WHERE CustomerName = '$customerName' OR Email = '$email'";
$result = $conn->query($sql_check);

$errorMessages = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['CustomerName'] === $customerName && !in_array("Username is already in use.", $errorMessages)) {
            $errorMessages[] = "Username is already in use.";
        }
        if ($row['Email'] === $email && !in_array("Email is already in use.", $errorMessages)) {
            $errorMessages[] = "Email is already in use.";
        }
    }
    
    // Store error messages in session
    $_SESSION['registererror_message'] = implode(" ", $errorMessages);
    header('Location: Homepage.php?error=2');
    exit();
}


// Insert data into the database
$sql = "INSERT INTO Customer (CustomerName, Email, PhoneNumber, password) VALUES ('$customerName', '$email', '$phoneNumber', '$hashedpassword')";

if ($conn->query($sql) === TRUE) {
    $customerID = $conn->insert_id;

    if (isset($_POST['pets'])) {
        $pets = $_POST['pets'];

        foreach ($pets as $pet) {
            $petName = $pet['name'];
            $species = $pet['species'];
            $dob = $pet['dob'];
            $weight = $pet['weight'];
            $allergies = isset($pet['allergies']) ? $pet['allergies'] : '';

            $sql_pets = "INSERT INTO Pets (PetName, Species, DateOfBirth, Weight, Allergies, CustomerID) VALUES ('$petName', '$species', '$dob', '$weight', '$allergies', '$customerID')";
            if (!$conn->query($sql_pets)) {
                echo "Error: " . $conn->error;
            }
        }
    }

    echo "<script>
        alert('Your account has been created successfully.');
        window.location.href = 'Homepage.php';
    </script>";

} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
