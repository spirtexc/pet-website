<?php
session_start();
session_destroy();
echo json_encode(["success" => true]);  // Respond with success message
?>