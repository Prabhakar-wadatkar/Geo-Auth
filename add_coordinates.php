<?php
// Establish a connection to the MySQL database
include_once 'config.php';

// Check if latitude and longitude values are present in the POST request
if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
    // Extract latitude and longitude from the request
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Check if the coordinates already exist in the table
    $sql = "SELECT COUNT(*) AS count FROM stored_coordinates WHERE latitude = ? AND longitude = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dd", $latitude, $longitude);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    if ($count == 0) {
        // Coordinates do not exist, insert them into the table
        $sql = "INSERT INTO stored_coordinates (latitude, longitude) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dd", $latitude, $longitude);
        $stmt->execute();
        echo "Coordinates saved successfully.";
    } else {
        echo "Coordinates already exist.";
    }

    $stmt->close();
}
?>
