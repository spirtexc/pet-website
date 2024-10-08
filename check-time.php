<?php
    include 'config.php';


    if (!isset($_POST['date'])) {
        echo json_encode(["error" => "No date provided"]);
        exit();
    }


    $appointmentDate = $_POST['date'];


    $stmt = $conn->prepare("SELECT BookingTime FROM Bookings WHERE BookingDate = ?");
    if (!$stmt) {
        echo json_encode(["error" => "Failed to prepare statement: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $appointmentDate);
    if (!$stmt->execute()) {
        echo json_encode(["error" => "Failed to execute query: " . $stmt->error]);
        exit();
    }

    $result = $stmt->get_result();


    $allTimes = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'];


    $bookedTimes = [];
    while ($row = $result->fetch_assoc()) {

        $time = date('H:i', strtotime($row['BookingTime']));
        $bookedTimes[] = $time;
    }


    $availableTimes = array_diff($allTimes, $bookedTimes);


    echo json_encode(array_values($availableTimes));


    $stmt->close();
    $conn->close();
?>
