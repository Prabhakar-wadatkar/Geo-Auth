
<?php
// Retrieve latitude and longitude from the AJAX request
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

if (isset($latitude) && isset($longitude)) {
    // Store the coordinates in PHP variables or perform any desired processing
    $storedLatitude = $latitude;
    $storedLongitude = $longitude;

    // Display the stored coordinates
    
    echo "Stored Latitude: " . $storedLatitude . "<br>";
    echo "Stored Longitude: " . $storedLongitude . "<br>";
}

// Retrieve the stored coordinates from the database
$sql = "SELECT latitude, longitude FROM coordinates";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Current coordinates
    $currentLatitude = $latitude;
    $currentLongitude = $longitude;

    // Compare the current coordinates with stored coordinates
    $matchFound = false;
    while ($row = $result->fetch_assoc()) {
        $storedLatitude = $row['latitude'];
        $storedLongitude = $row['longitude'];

        if ($storedLatitude == $currentLatitude && $storedLongitude == $currentLongitude) {
            // Coordinates match
            $matchFound = true;
            break;
        }
    }

    // Set the button status based on the match
    $buttonStatus = $matchFound ? 'active' : 'disabled';

    if ($matchFound) {
        echo "Coordinates match!";
    } else {
        echo "Coordinates do not match.";
    }
} else {
    echo "No coordinates found in the database.";
}
?>
