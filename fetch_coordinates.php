<?php
// Establish a connection to the MySQL database
include_once 'config.php';

// Fetch the distinct matching coordinates directly from the database query
$sql = "SELECT DISTINCT stored.latitude, stored.longitude
        FROM stored_coordinates AS stored
        JOIN current_coordinates AS current
        ON SUBSTRING(stored.latitude, 0, 5) = SUBSTRING(current.latitude, 0, 5)
        AND stored.longitude = current.longitude";

$matchingCoordinates = array(); // Initialize the array to store the matching coordinates

// Execute the query and fetch the matching coordinates
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $matchingCoordinates[] = $row;
  }
}

// Output the matching coordinates
if (!empty($matchingCoordinates)) {
  $previousCoordinates = ''; // Variable to store the previously displayed coordinates

  foreach ($matchingCoordinates as $matching) {
    $latitude = $matching['latitude'];
    $longitude = $matching['longitude'];

    // Extract the first five digits of latitude and longitude
    $latitudeFirstFive = substr($latitude, 0, 7);
    $longitudeFirstFive = substr($longitude, 0, 7);

    // Check if the coordinates are the same as the previous ones
    $currentCoordinates = $latitudeFirstFive . '_' . $longitudeFirstFive;
    if ($currentCoordinates !== $previousCoordinates) {
      // Display the coordinate
      echo "Matching Coordinate: Latitude: " . $latitudeFirstFive . ", Longitude: " . $longitudeFirstFive . "<br><br>";

      // Update the previous coordinates
      $previousCoordinates = $currentCoordinates;
    }
  }
} else {
  echo "No matching coordinates found." . "<br><br>";
}

// Close the database connection
$conn->close();
?>
