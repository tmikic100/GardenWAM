<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	//checks if http method is get and if it isn't it kills the program
	if(!($_SERVER['REQUEST_METHOD'] === "GET")){
		http_response_code(405);
		die("method not supported");
	}
	$plant = "";
	$type = "";
	//checks all headers of http request
	foreach (getallheaders() as $name => $value) {
		if($name === "plant"){
			$plant = $value;
		}
		if($name === "type"){
			$type = $value;
		}
	}
	if($plant === ""){
		die;
	}
	//if type isn't set then it returns information for all plants
	if($type == ""){
		$sql = "SELECT * FROM information WHERE PlantId = '$plant' ORDER BY id DESC";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()){
				$data = ["Name" =>  $row["Name"],
				"Time" => $row["TimeStamp"]];
				$gt[] = $data;
				unset($data);
			}
			echo json_encode($gt);;
		} else {
			echo "0 results";
		}
	//if time is set then it returns information for that plant
	}else{
		$sql = "SELECT *  FROM `information` WHERE `PlantId` = '$plant' AND `Name` = '$type'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$data = ["Name" =>  $row["Name"],
				"Time" => $row["TimeStamp"]];
			echo json_encode($data);
		}else{
			echo "0 results";
		}
	}
?>