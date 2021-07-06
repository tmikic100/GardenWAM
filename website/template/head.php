<?php
class head {
	private $name = 'yeet';
	
	function __construct($name) {
		$this->name = $name;
	}
	function get_name() {
		return $this->name;
	}
	function set_name($title) {
		$this->name = $title;
	}
	function get_html() {
		echo "
		<head>
			<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>
			<script src='http://static.pureexample.com/js/flot/excanvas.min.js'></script>
			<script src='http://static.pureexample.com/js/flot/jquery.flot.min.js'></script>
			<script src='js/index.js'></script>
			<link rel='stylesheet' type = 'text/css' href='css/general.css'>
			<link rel='stylesheet' type = 'text/css' href='css/index.css'>
			<title>".$this->name."</title> 
		</head>";
	}
	function get_html_pages() {
		echo "
		<head>
			<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>
			<script src='http://static.pureexample.com/js/flot/excanvas.min.js'></script>
			<script src='http://static.pureexample.com/js/flot/jquery.flot.min.js'></script>
			<link rel='stylesheet' type = 'text/css' href='../css/general.css'>
			<title>".$this->name."</title> 
		</head>";
	}
}
?>