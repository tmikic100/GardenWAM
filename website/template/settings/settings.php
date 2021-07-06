<?php
class  settings {
	private $plant;
	private $panel = array();
	function __construct($plant){
		$this->plant = $plant;
	}
	function add_expansionPanel($panel, $name){
		if(get_Class($panel) === "expansionPanel"){
			$this->panel["$name"] = $panel;
		}else{
			echo "wrong data type";
		}
	}
	function remove_expansionPanel($name){
		unset($this->panel["$name"]);
	}
	function get_expansionPanel($name){
		return $this->panel["$name"];
	}
	function get_expansionPanels(){
		return $this->panel;
	}
	function get_HTML(){
		echo "<div class='expansionSection'  style='width: 300px;'>
		<script type='text/javascript' plant='".$this->plant."' src='../template/settings/settings.js'></script>";
		foreach($this->panel as $key => $value){
			$value->get_HTML();
		}
		echo "</div>";
	}
}
class settingsObject{
	private $type;
	private $data;
	function __construct($type,$data){
		$this->type = $type;
		$this->data = $data;
	}
	
	function get_Type(){
		return $this->type;
	}
	function set_Type($type){
		$this->type = $type;
	}
	function get_Data(){
		return $this->data;
	}
	function set_Data($data){
		$this->data = $data;
	}
	function get_HTML(){
		switch($this->type){	
			case "Radio":
				echo "<label>".$this->data['name'].":</label>
				<div class='radioSection'>";
				$i = true;
				foreach($this->data['data'] as $key => $value){
					echo "<label class='container'>".str_replace ("_"," ",$key)."
					<input type='radio' name='".$this->data['name']."' value='$value'>
					<span class='checkmark'></span>
					</label>";
				}
				echo "</div>";
				break;
			case "Select":
				echo "<label>".$this->data['name'].":</label>
				<select id='".$this->data['name']."Select'>";
				foreach($this->data['data'] as $key => $value){
					echo "<option value='$value'>".str_replace ("_"," ",$key)."</option>";
				}
				echo "</select>";
				break;
			case "Slider":
				echo "<label>".str_replace ("_"," ",$this->data["name"]).":</label>
				<input type='range' min='".$this->data['data']['min']."' max='".$this->data['data']['max']."' value='0'id='".$this->data['name']."Slider'><br>
				<label id='".$this->data['name']."Value'>Value: 0</label>";
				break;
			default:
				echo "Type is invalid";
				break;
			
		}
	}
}
class expansionPanel{
	private $panel = array();
	private $panelName;
	function __construct($name){
		$this->panelName = $name;
	}
	function get_name(){
		return $this->panelName;
	}
	function set_name($name){
		$this->panelName = $name;
	}
	function add_Setting($settings, $name){
		if(get_Class($settings) === "settingsObject"){
			$this->panel["$name"] = $settings;
		}else{
			echo "wrong data type";
		}
	}
	function remove_setting($name){
		unset($this->panel["$name"]);
	}
	function get_setting($name){
		return $this->panel["$name"];
	}
	function get_settings(){
		return $this->panel;
	}
	function get_HTML(){
		echo "<div class='expansionPanel'>
			<div class='expansionPanelHeader noselect'>
			<h3>$this->panelName</h3>
			</div>
			<div class='expansionPanelBody'>";
		foreach($this->panel as $key => $value){
			echo "<div class='settingsItem ".strtolower($value->get_Type())."Item'>";
			$value->get_HTML();
			echo "</div>";
		}
		echo "</div>
		</div>";
	}
}
$url = 'http://127.0.0.1/api/Settings/read.php';

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . 
					 "Authorization: Basic R2FyZGVuV0FNOmF6Y2MzN1BL\r\n" .
					 "plant: Coffee\r\n",
        'method'  => 'GET'
    )
);
?>