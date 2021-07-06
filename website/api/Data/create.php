<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	//checking if request method is post if it isn't it kills program with unsupported http method
	if(!($_SERVER['REQUEST_METHOD'] === "POST")){
		http_response_code(405);
		die("method not supported");
	}
	$plant = "";
	//checking each of header in http request
	foreach (getallheaders() as $name => $value) {
		if($name === "plant"){
			$plant = $value;
		}
		//if empty header is set in creates new data entry but empty so if there is new plant created it program can find it
		if($name === "empty"){
			$sql = "INSERT INTO data (PlantId, Empty)
			VALUES ('$plant', TRUE)";
			if ($conn->query($sql) === TRUE) {
				die("New record created successfully");
			} else {
				die("Error: " . $sql . "<br>" . $conn->error);
			}
		}
	}
	$content = trim(file_get_contents("php://input"));
	$decoded = json_decode($content, true);
	//if plant isn't set then it kills the program
	if($plant === ""){
		http_response_code(400);
		die("no plant");
	}
	//checks if all data is set and if it isn't then it kills the program with invalid request
	if(!($plant === "") && isset($decoded['AirTemp']) === true && isset($decoded['SoilTemp']) === true  && isset($decoded['Moisture']) === true && isset($decoded['Humidity']) === true){
		$AirTemp = $decoded['AirTemp'];
		$SoilTemp = $decoded['SoilTemp'];
		$Moisture = $decoded['Moisture'];
		$Humidity = $decoded['Humidity'];
		$sql = "INSERT INTO data (PlantId, AirTemp, SoilTemp, Humidity, Moisture)
		VALUES ('$plant', '$AirTemp', '$SoilTemp', '$Humidity', '$Moisture')";
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}else{
		http_response_code(400);
		echo "invalid data";
	}
?>