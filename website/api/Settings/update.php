<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	$content = trim(file_get_contents("php://input"));
	$decoded = json_decode($content, true);
	$sql;
	$name;
	$plant = "";
	//checks if http method is put and if it isn't it kills the program
	if(!($_SERVER['REQUEST_METHOD'] === "PUT")){
		http_response_code(405);
		die("method not supported");
	}
	foreach (getallheaders() as $name => $value) {
		if($name === "plant"){
			$plant = $value;
		}
	}
	//checks if current value and Name is set for specfic plant if they aren't it kills the program
	if($plant === ""){
		http_response_code(400);
		die("Plant isn't set");
	}
	if(isset($decoded['Current']) === true){
		$Current = $decoded['Current'];
	}else {
		http_response_code(400);
		die("Current value isn't set");
	}
	if(isset($decoded['Name'])){
		$name = $decoded['Name'];
	}else{
		http_response_code(400);
		die("Name isn't set");
	}
	//updates specfic setting for specfic plant with current value
	$sql = "UPDATE settings
	SET Current = '$Current'
	WHERE Name = '$name' AND PlantId = '$plant'";
	if ($conn->query($sql) === true) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
?>