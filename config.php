<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pet_website";
 



$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


return $conn;
?>
