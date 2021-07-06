<?php
$servername = "localhost";
$username = "GardenWAM";
$password = "XXXXXXX";
$database = "GardenWAM";
// Create connection
$conn = new mysqli($servername, $username, $password,$database);
// Check connection
if ($conn->connect_error) {
	http_response_code(500);
	die("Connection failed: " . $conn->connect_error);
}
?>
