<?php
// Establish a connection to the MySQL database
include_once 'config.php';

// Start the session
session_start();

// Infinite loop
while (true) {
    // Extract latitude and longitude from the request
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Check if the coordinates already exist in the table
    $sql = "SELECT COUNT(*) AS count FROM current_coordinates WHERE latitude = ? AND longitude = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dd", $latitude, $longitude);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    if ($count == 0) {
        // Coordinates do not exist, insert them into the table
        $sql = "INSERT INTO current_coordinates (latitude, longitude) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dd", $latitude, $longitude);
        $stmt->execute();
        $_SESSION['latitude'] = $latitude;
        $_SESSION['longitude'] = $longitude;
        echo "Coordinates saved successfully.";
    } else {
        echo "Coordinates already exist.";
    }

    $stmt->close();

    // Sleep for 2 seconds
    // sleep(2);

    // Delete duplicate latitude records
    // Delete all records except the latest 2
$sql = "DELETE FROM current_coordinates WHERE id NOT IN (SELECT id FROM (SELECT id FROM current_coordinates ORDER BY id DESC LIMIT 2) AS latest)";
$conn->query($sql);
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->close();
}


?>