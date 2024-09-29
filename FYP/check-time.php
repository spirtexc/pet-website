<?php
    include 'config.php';

    // Check if POST data is set
    if (!isset($_POST['date'])) {
        echo json_encode(["error" => "No date provided"]);
        exit();
    }

    // Get the selected date from POST data
    $appointmentDate = $_POST['date'];

    // Prepare and execute SQL query to fetch booked times
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

    // Define all possible time slots in 'HH:MM' format
    $allTimes = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'];

    // Collect booked times and convert them to 'HH:MM' format
    $bookedTimes = [];
    while ($row = $result->fetch_assoc()) {
        // Convert 'HH:MM:SS' to 'HH:MM'
        $time = date('H:i', strtotime($row['BookingTime']));
        $bookedTimes[] = $time;
    }

    // Calculate available times by excluding booked times
    $availableTimes = array_diff($allTimes, $bookedTimes);

    // Return available times as JSON
    echo json_encode(array_values($availableTimes));

    // Close connection
    $stmt->close();
    $conn->close();
?>
