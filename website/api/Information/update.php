<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	$content = trim(file_get_contents("php://input"));
	$decoded = json_decode($content, true);
	$sql;
	$type = "";
	$plant = "";
	$type = "";
	//checks if http method is put and if it isn't it kills the program
	if(!($_SERVER['REQUEST_METHOD'] === "PUT")){
		http_response_code(405);
		die("method not supported");
	}
	foreach (getallheaders() as $key => $value) {
		if($key === "plant"){
			$plant = $value;
		}
	}
	if($plant === ""){
		die;
	}
	//if type isn't set then it kills the program
	if(isset($decoded['type']) === true){
		$type = $decoded['type'];
	}else {
		http_response_code(400);
		die("invaild data");
	}
	$sql = "UPDATE information
	SET TimeStamp = CURRENT_TIMESTAMP
	WHERE PlantId = '$plant' AND Name = '$type'";
	if ($conn->query($sql) === true) {
		echo "Record updated successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
?>