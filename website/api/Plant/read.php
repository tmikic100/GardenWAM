<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	//checks if http method is get and if it isn't it kills the program
	if(!($_SERVER['REQUEST_METHOD'] === "GET")){
		http_response_code(405);
		die("method not supported");
	}
	//returns all plants and their ports
	$sql = "SELECT * FROM plants ORDER BY Port ASC";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
			$data = ["Name" => $row["Name"],
			"Port" => $row["Port"]];
			$gt[] = $data;
			unset($data);
		}
		echo json_encode($gt);
	} else {
		echo "0 results";
	}
?>