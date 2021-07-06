<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	//checking if request method is get if it isn't it kills program with unsupported http method
	if(!($_SERVER['REQUEST_METHOD'] === "GET")){
		http_response_code(405);
		die("method not supported");
	}
	$plant = "";
	$amount = 0;
	$found = false;
	//checking each of header in http request
	foreach (getallheaders() as $name => $value) {
		if($name === "plant"){
			$plant = $value;
			$found = true;
		//if name is all api will return lastest value of each plant
		}else if($name === "all"){
			$current_cat = null;
			$sql = "SELECT data.`PlantId`, max(data.`TimeStamp`) as TimeStamp,data.`Empty`, data.`Moisture`,data.`Humidity`,data.`AirTemp`,data.`SoilTemp` FROM data GROUP BY data.`PlantId`";
			$result = $conn->query($sql);
			while($row = $result->fetch_assoc()){
				if ($row["PlantId"] != $current_cat) {
					if($row["Empty"] === "0"){
						$data = ["Plant" => $row["PlantId"],
						"TimeStamp" => $row["TimeStamp"],
						"Empty" => "0",
						"Humidity" => $row["Humidity"],
						"Moisture" => $row["Moisture"],
						"AirTemp" => $row["AirTemp"],
						"SoilTemp" => $row["SoilTemp"]];
					}else{
						$data = ["Plant" => $row["PlantId"],
						"Empty" => "1"];
					}
					$gt[] = $data;
					unset($data);
				}
			}
			echo json_encode($gt);
			die;
		}
		if($name === "amount"){
			$amount = $value;
		}	
	}
	//if there isn't plant header it kills the program
	if($found === false){
		http_response_code(400);
		die("invaild header");
	}
	//if amount is 0 aka not set then it returns last value for selected plant
	if($amount == 0){
		$sql = "SELECT * FROM data WHERE PlantId = '$plant' ORDER BY id DESC";
		$result = $conn->query($sql);if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$data = ["TimeStamp" => $row["TimeStamp"],
				"Humidity" => $row["Humidity"],
				"Moisture" => $row["Moisture"],
				  "AirTemp" => $row["AirTemp"],
				"SoilTemp" => $row["SoilTemp"]];
			echo json_encode($data);
		} else {
			echo "0 results";
		}
	//if amount is sent then it returns x amount of plants 
	}else{
		$sql  = "SELECT * FROM (SELECT * FROM data WHERE PlantId = '$plant' ORDER BY data.id  DESC LIMIT " . $amount . ") as t1 ORDER BY id ASC"; 
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()){
				$data = ["TimeStamp" => $row["TimeStamp"],
				"Humidity" => $row["Humidity"],
				"Moisture" => $row["Moisture"],
				  "AirTemp" => $row["AirTemp"],
				"SoilTemp" => $row["SoilTemp"]];
				$gt[] = $data;
				unset($data);
			}
			echo json_encode($gt);
		} else {
			echo "0 results";
		}
	}
?>