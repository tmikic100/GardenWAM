<?php
//checking for basic login if doesn't have login it kills the program
if(!($_SERVER['PHP_AUTH_USER'] === "GardenWAM" && $_SERVER['PHP_AUTH_PW'] === "XXXXXX")){
	http_response_code(401);
	die("Unauthorized");
}

?>
