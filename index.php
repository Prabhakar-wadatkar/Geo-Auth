<?php
include 'config.php';
session_start();
error_reporting(0);
?>
<!doctype html>
<html lang="en">
  <head>
  <title>GeoAuth</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYN8_WJdHGYwS_Shc8kp8q3f_2gEBHjb8&callback=initMap" async defer></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://kit.fontawesome.com/a75cb9b5b7.js" crossorigin="anonymous"></script>  
</head>
  <body>

     <div class="row" style="padding-top: 100px;">
    <div class="col">
    </div>
  </div>
  <div class="row">
    <div class="col">
    </div>
    <div class="col">
      <div class="card">
        <div class="card-body">
          <div>

<style>
#map {
  height: 100px;
  width: 500px;
}
</style>
<main>
  <div id="map" class="card" data-chookies="map"></div>
</main>
<p id="coordinates"></p>

<script>
    let map, marker, circle;
    window.addEventListener('load', function () {
      initMap();
     });
    function initMap() {
      map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 22.3511148, lng: 78.6677428},
        zoom: 4
      });
  
      marker = new google.maps.Marker({
        map: map,
        draggable: false
      });
  
      if (navigator.geolocation) {
        navigator.geolocation.watchPosition(success, error);
      } else {
        alert('Geolocation is not supported by this browser.');
      }
      
      
    }
  
  function success(position) {
  const lat = position.coords.latitude;
  const lng = position.coords.longitude;
  const accuracy = position.coords.accuracy;

  const latLng = new google.maps.LatLng(lat, lng);

  map.setCenter(latLng);
  map.setZoom(17);

  if (marker) {
    marker.setMap(null);
  }

  marker = new google.maps.Marker({
    position: latLng,
    map: map
  });

  if (circle) {
    circle.setMap(null);
  }

  circle = new google.maps.Circle({
  map: map,
  center: latLng,
  radius: 20,
  fillColor: '#4285F4',
  fillOpacity: 0.2,
  strokeColor: '#4285F4',
  strokeOpacity: 0.4,
  strokeWeight: 2
});


  // Save the coordinates
  saveCoordinates(lat, lng);
}


function error(error) {
  let errorMessage = 'Unable to retrieve your location.';

  switch (error.code) {
    case error.PERMISSION_DENIED:
      errorMessage = 'Location access is denied. Please enable location access in your browser settings.';
      break;
    case error.POSITION_UNAVAILABLE:
      errorMessage = 'Location information is unavailable.';
      break;
    case error.TIMEOUT:
      errorMessage = 'The request to get location timed out.';
      break;
    case error.UNKNOWN_ERROR:
      errorMessage = 'An unknown error occurred while retrieving your location.';
      break;
  }

  alert(errorMessage);
}

  
    function saveCoordinates(latitude, longitude) {
      // Send an AJAX request to save the coordinates
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "save_coordinates.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      const params = `latitude=${latitude}&longitude=${longitude}`;
      xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
          console.log("Coordinates saved successfully");
        }
      };
      xhr.send(params);
    }
  </script>
</div>

<!-- form open -->

<?php
 
 
// Fetch the distinct matching coordinates directly from the database query
$sql = "SELECT stored.latitude, stored.longitude
FROM stored_coordinates AS stored
JOIN (
    SELECT latitude, longitude
    FROM current_coordinates
    ORDER BY id DESC
    LIMIT 1
) AS current
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
    $matchFound = false;
    foreach ($matchingCoordinates as $matching) {
        $latitude = $matching['latitude'];
        $longitude = $matching['longitude'];

        // Extract the first five digits of latitude and longitude
        $latitudeFirstFive = substr($latitude, 0, 9);
        $longitudeFirstFive = substr($longitude, 0, 9);

        // Check if the coordinates are the same as the previous ones
        $currentCoordinates = $latitudeFirstFive . '_' . $longitudeFirstFive;
        if ($currentCoordinates !== $previousCoordinates) {
            // Display the coordinate
            echo "Location Authorized: Latitude: " . $latitudeFirstFive . ", Longitude: " . $longitudeFirstFive . "<br><br>";

            if (isset($_SESSION['username'])) {
              header("Location: ./Dashbord/dashboard.php");
          }
          
          if (isset($_POST['submit'])) {
            $email = $_POST['email'];
            $password = md5($_POST['password']);
            
          
            $sql = "SELECT * FROM users 
            WHERE email='$email' 
            AND password='$password'";
            
              $result = mysqli_query($conn, $sql);
              if ($result->num_rows > 0) {
                $row = mysqli_fetch_assoc($result);
        
                // // Update the flag in the users table
                // $userId = $row['id'];
                // $updateSql = "UPDATE users SET flag = 1 WHERE id = $userId";
                // mysqli_query($conn, $updateSql);
        
                // Save the current coordinates and username in the login_users table
                // $username = $row['username'];
                // $latitude = $_SESSION['latitude'];
                // $longitude = $_SESSION['longitude'];
                // $timestamp = date('Y-m-d H:i:s');
        
          
                // $insertSql = "INSERT INTO login_users (username, latitude, longitude, timestamp) VALUES ('$username', '$latitude', '$longitude', '$timestamp')";
                // mysqli_query($conn, $insertSql);
                
              header("Location: ./Dashbord/dashboard.php");
            } else {
              echo "<script>alert('Woops! Email or Password is Wrong.')</script>";
            }
          }
            // Update the previous coordinates
            $previousCoordinates = $currentCoordinates;
            $matchFound = true;

            // Exit the loop
            break;
        }
    }
} else {
  
    echo '<div style="text-align: center; font-size: 15px;  color: black;">Location not match.        ';
    echo '<i class="fas fa-sync-alt fa-arrows-rotate" onclick="refreshPage()"></i>'."<br><br>".'</div>';
}

// Close the database connection
?>

	<div class="container">
		<form action="" method="POST" class="login-email">
			<p class="login-text" style="font-size: 2rem; font-weight: 800;">Login</p>
			<div class="input-group">
				<input type="email" placeholder="Email" name="email" value="<?php echo $email; ?>" required>
			</div>
			<div class="input-group">
				<input type="password" placeholder="Password" name="password" value="<?php echo $_POST['password']; ?>" required>
			</div>
			<div class="input-group">
      <button name="submit" class="btn">Login</button>
			</div>
			<p class="login-register-text">Don't have an account? <a href="register.php">Register Here</a>.</p>
		</form>
	</div>

 <!-- Form closed -->
          <div>
         </div>
        </div>
        <div style="text-align: center; font-size: 10px; margin-bottom: 12px; color: black;">
  <a href="http://prabhakarwadatkar.in/" style="color: black; text-decoration: none;">Prabhakar D. Wadatkar</a> &copy; 2023. All rights reserved.
</div>


       </div>
      </div>
    <div class="col">
    </div>
  </div>
 </div>
</div>
<script>
    function refreshPage() {
        // Get the refresh icon element
        var icon = document.querySelector('.fa-sync-alt');

        // Add the fa-spin class to initiate the spinning animation
        icon.classList.add('fa-spin');

        // Wait for 5 seconds (5000 milliseconds) before refreshing the page
        setTimeout(function() {
            location.reload();
        }, );
    }
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  </body>
</html>

