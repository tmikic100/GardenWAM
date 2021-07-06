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
	//if port isn't set then it kills the program
	if(isset($decoded['Port']) === true){
		$Port = $decoded['Port'];
		$sql = "INSERT INTO plants (Name, Port)
		VALUES ('$plant', '$Port')";
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