<?php
class  plantSummary {
	private $plants = array();
	//constructor which gets all plants and puts then in a array if there isn't any plants it return "please add a plant" info text
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
				$this->plants[$decoded[$i]["Name"]] = new plant($decoded[$i]["Name"]);
			}
		}else{
			echo "<div class='infoText'>
					<h2>Please add a plant</h2>
				</div>";
		}
	}
	/*
	 *function for getting plants array
	 *output array plants
	 */
	function get_Plants(){
		return $this->plants;
	}
	/*
	 *function setting a item in plants
	 *input specific plant and name of that plant
	 */
	function add_Plant($plant, $name){
		$this->plants[$name] = $plant;
 	}
	/*
	 *function removing a item in plants
	 *input specific name of that plant that you want to remove
	 */
	function remove_Plant($name){
		unset($this->plants["$name"]);
	}
	
	//function which returns specific HTML for all plants in array
	function get_HTML(){
		echo "<div class='pageSummaryContiner'>";
		foreach($this->plants as $key => $value){
			$value->get_HTML();
		}
		echo "</div>";
	}
}
//plant class 
class plant{
	private $name;
	private $data = array();
	//constructor which gets gets last data for specific plant
	function __construct($name){
		$this->name = $name;
		$url = 'http://127.0.0.1/api/Data/read.php';
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . 
							"Authorization: Basic R2FyZGVuV0FNOmF6Y2MzN1BL\r\n" .
							"all:0\r\n",
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
		for($i = 0; $i < sizeof($decoded); $i++){
			if($this->name === $decoded[$i]["Plant"]){
				break;
			}
		}
		foreach($decoded[$i] as $name => $value){
			if(!($name === "Plant")){
				$this->data[$name] = $value;
			}
		}
	}
	/*
	 *function which returns name of a plant
	 *output text plant_name
	 */
	function get_Name(){
		return $this->name;
	}
	/*
	 *function which sets name of a plant
	 *input text plant_name
	 */
	function set_Name($name){
		$this->name = $name;
	}
	/*
	 *function which returns data of a plant
	 *output array plant_data
	 */
	function get_Data(){
		return $this->data;
	}
	/*
	 *function which sets data of a plant
	 *output array plant_name
	 */
	function set_Data($data){
		$this->data -> $data;
	}
	//return HTML for specific plant and its data
	function get_HTML(){
		echo "<div class='pageSummaryItem'>
		<div class='pageSummaryItemTitle'>
		<script type='text/javascript' src='../template/plantSummary/plantSummary.js'></script>
		<h2>".$this->name."</h2>
		</div>";
		if($this->data["Empty"] === "0"){
			foreach($this->data as $name => $value){
				if(!($name === "TimeStamp")){
					if(!($name === "Empty")){
						echo "<div class=pageSummaryItemText id='".$name."Id'>";
						if (strpos($name, 'Temp') !== false) {
							echo "<h4>". str_replace("Temp"," Temperature",$name).": $value*C</h4>";
						}else{
							echo "<h4>$name: $value%</h4>";
						}
						echo "</div>";
					}
				}
			}
			echo "<div class=pageSummaryItemText id='TimeStampId'>
					<h4>Last updated at ".date("H:i", strtotime($this->data["TimeStamp"]))."</h4>
				</div>
			</div>";
		}else{
			echo "<div class=pageSummaryItemText id='EmptyDataId'>
					<h4>There isn't any data for this plant</h4>
				</div>
			</div>";
		}
	}
}
?>