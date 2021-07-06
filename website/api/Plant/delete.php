<?php
	include_once '../Config/connect.php';
	include_once '../Config/login.php';
	//checks if http method is post and if it isn't it kills the program
	if(!($_SERVER['REQUEST_METHOD'] === "DELETE")){
		http_response_code(405);
		die("method not supported");
	}
	$port = "";
	foreach (getallheaders() as $name => $value) {
		if($name === "port"){
			$port = $value;
		}
	}
	if($port === ""){
		die;
	}
	$sql = "DELETE FROM plants
	WHERE Port = $port";
	echo "removed $port";
	if ($conn->query($sql) === TRUE) {
		echo "Record deleted successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
?>