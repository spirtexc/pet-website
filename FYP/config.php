<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pet_website";
 // Notice no quotes around the number

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Return the connection so it can be used elsewhere
return $conn;
?>
