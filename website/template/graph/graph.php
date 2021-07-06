<?php
class graph {
	private $names = array("Temp","Humd");
	private $vars = array("Air_Temp","Soil_Temp","Humidity","Moisture");
	private $plant = 'coffee';
	/*
	 *construtor for graph class
	 *it sets plant name
	 *input text plant_name
	 */
	function __construct($plant){
		$this->plant = $plant;
	}
	/*
	 *function for getting plant name
	 *output text plant_name
	 */
	function get_plant(){
		return $this->plant;
	}
	/*
	 *function for seting plant name
	 *it sets plant name
	 *input text plant_name
	 */
	function set_plant($plant){
		$this->plant = $plant;
	}
	//function which returns a HTML based on template where specific fields are replaced with set values
	function get_html() {
		echo "
		<script type='text/javascript' names='".json_encode($this->names)."' vars='".json_encode($this->vars)."' plant='".$this->plant."' src='../template/graph/graph.js'></script>
		<div class = 'chartContiner'> 
			<div id='".$this->names[0]."Chart' style='height: 400px; width: 500px;' class='noselect'></div>
			<div class='chartText'>
				<div class='text'>
					<h2>".str_replace("_"," ",$this->vars[0])."</h2>
					<p id='".$this->vars[0]."Max'></p>
					<p id='".$this->vars[0]."Min'></p>
					<p id='".$this->vars[0]."Avg'></p>
				</div>
				<div class='text'>
					<h2>".str_replace("_"," ",$this->vars[1])."</h2>
					<p id='".$this->vars[1]."Max'></p>
					<p id='".$this->vars[1]."Min'></p>
					<p id='".$this->vars[1]."Avg'></p>
				</div>
			</div>
		</div>
		<div class = 'chartContiner'> 
			<div id='".$this->names[1]."Chart' style='height: 400px; width: 500px;' class='noselect'></div>
			<div class='chartText'>
				<div class='text'>
					<h2>".$this->vars[2]."</h2>
					<p id='".$this->vars[2]."Max'></p>
					<p id='".$this->vars[2]."Min'></p>
					<p id='".$this->vars[2]."Avg'></p>
				</div>
				<div class='text'>
					<h2>".$this->vars[3]."</h2>
					<p id='".$this->vars[3]."Max'></p>
					<p id='".$this->vars[3]."Min'></p>
					<p id='".$this->vars[3]."Avg'></p>
				</div>
			</div>
		</div>";
	}
}
?>

