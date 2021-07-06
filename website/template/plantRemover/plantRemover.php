<?php
class  plantRemover {
	private $plants = array();
	//constructor for plantRemover class which gets all current plants and adds them into array 
	function __construct(){
		$url = 'http://127.0.0.1/api/Plant/read.php';
		
		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . 
							"Authorization: Basic R2FyZGVuV0FNOmF6Y2MzN1BL\r\n" ,
				'method'  => 'GET'
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE){
			http_response_code(400);
			die("Something went wrong");
		}
		$decoded = json_decode($result, true);
		if($decoded != null){
			for($i = 0; $i < sizeof($decoded); $i++){
				$this->plants[] = ["Name" => $decoded[$i]["Name"],
				"Port" => $decoded[$i]["Port"]];
			}
		}
	}
	//function which returns HTML with all plants in select HTML element
	function get_HTML(){
		if($this->plants != null){
			echo "<div class = 'plantRemoverContiner'>
			<div class='plantRemoverItem'>
			<script type='text/javascript' src='../template/plantRemover/plantRemover.js'></script>
			<select id = 'removeSelect'>";
				for($i = 0; $i < sizeof($this->plants); $i++){
					echo "<option value=";
					echo $i+1;
					echo ">P";
					echo $this->plants[$i]["Port"]+1;
					echo " ";
					echo $this->plants[$i]["Name"];
					echo "</option>";
				}
			echo "</select>
			</div>
			<div class='plantRemoverItem'>
			<input id='removeButton' type='button' value='Remove' style='width: 80px; height:20px;'>
			</div>
			</div>";
		}
	}
}
?>