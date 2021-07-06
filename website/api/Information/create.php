<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	//checks if http method is post and if it isn't it kills the program
	if(!($_SERVER['REQUEST_METHOD'] === "POST")){
		http_response_code(405);
		die("method not supported");
	}
	$plant = "";
	foreach (getallheaders() as $name => $value) {
		if($name === "plant"){
			$plant = $value;
		}
	}
	if($plant === ""){
		die;
	}
	$content = trim(file_get_contents("php://input"));
	$decoded = json_decode($content, true);
	//checks if name and time is set and if it isn't then it kills the program
	if(isset($decoded['Name']) === true && isset($decoded['Time']) === true){
		$Name = $decoded['Name'];
		$Time = $decoded['Time'];
		$sql = "INSERT INTO information (PlantId, Name, TimeStamp)
		VALUES ('$plant', '$Name', '$Time')";
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