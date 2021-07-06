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
	//checks if all values are set for settings and if they aren't it kills the program
	if(isset($decoded['Name']) === true && isset($decoded['SettingGroup']) === true && isset($decoded['Unit']) === true && isset($decoded['Type']) === true && isset($decoded['Data']) === true){
		$Name = $decoded['Name'];
		$SettingGroup = $decoded['SettingGroup'];
		$Unit = $decoded['Unit'];
		$Type = $decoded['Type'];
		$Data = $decoded['Data'];
		$sql = "INSERT INTO settings (PlantId, Name, SettingGroup, Unit, Type, Data)
		VALUES ('$plant', '$Name', '$SettingGroup', '$Unit', '$Type', '$Data')";
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