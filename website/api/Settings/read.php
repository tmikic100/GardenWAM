<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	//checks if http method is get and if it isn't it kills the program
	if(!($_SERVER['REQUEST_METHOD'] === "GET")){
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
		echo "yeet";
		die;
	}
	//returns all settings for specified plant
	$sql = "SELECT * FROM settings WHERE PlantId = '$plant' ORDER BY id DESC";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
			$data = ["Name" => $row["Name"],
			"SettingGroup" => $row["SettingGroup"],
			"Unit" => $row["Unit"],
			"Type" => $row["Type"],
			"Data" =>  json_decode($row["Data"]),
			"Current" => $row["Current"],
			"Dependency" => $row["Dependency"]];
			$gt[] = $data;
			unset($data);
		}
		echo json_encode($gt);
	} else {
		echo "0 results";
	}
?>