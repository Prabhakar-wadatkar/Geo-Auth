<?php

// Database connection parameters
$host = 'your_database_host';
$username = 'your_username';
$password = 'your_password';
$database = 'your_database_name';

// Create a new MySQLi instance
$mysqli = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Define the API endpoint to fetch coordinates
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/api/coordinates') {
    // Query the database to fetch coordinates
    $query = "SELECT latitude, longitude FROM coordinates_table LIMIT 1"; // Modify table and column names accordingly
    $result = $mysqli->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $coordinates = $result->fetch_assoc();
            header('Content-Type: application/json');
            echo json_encode($coordinates);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Coordinates not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred']);
    }

    // Close the database connection
    $mysqli->close();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
