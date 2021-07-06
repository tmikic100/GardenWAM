<?php
class navBar{
	private $tabs = array("Home","About");
	
	function __construct(){
		//get all items inside Pages folder
		if($this->get_dir_path() == 1){
			if($handle = opendir('pages')) {
				$about = array_pop($this->tabs);
				while (false !== ($entry = readdir($handle))) {
				
				if ($entry != "." && $entry != ".." && $entry != "About.php") {
					array_push($this->tabs,str_replace(".php","",$entry));
				}
			}
			closedir($handle);
			array_push($this->tabs,$about);
			}
		}else{
			if($handle = opendir('.')) {
				$about = array_pop($this->tabs);
				while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && $entry != "About.php") {
					array_push($this->tabs,str_replace(".php","",$entry));
				}
			}
			closedir($handle);
			array_push($this->tabs,$about);
			}
		}
	}
	
	
	function get_tabs(){
		return $this->tabs;
	}
	
	function get_dir_path() {
		$cur = str_replace($_SERVER['DOCUMENT_ROOT']."/","",$_SERVER["SCRIPT_FILENAME"]);
		echo "<script>console.log('".$cur."');</script>";
		if($cur === "index.php"){
			return 1;
		}
		return 0;
	}
	
	function get_tabs_html(){
		$cur = str_replace(".php","",str_replace("pages/","",str_replace($_SERVER['DOCUMENT_ROOT']."/","",$_SERVER["SCRIPT_FILENAME"])));
		echo "<script>console.log('".$cur."');</script>";
		if($this->get_dir_path() == 1){
			echo "<li><a class='active noselect'>Home</li>";
			for($i = 1; $i < sizeof($this->tabs); $i++){
				echo "<li><a href='pages/".$this->tabs[$i].".php'>".$this->tabs[$i]."</a></li>";
			}
		}else{
			echo "<li><a href='../index.php'>Home</li>";
			for($i = 1; $i < sizeof($this->tabs); $i++){
				if($cur === $this->tabs[$i]){
					echo "<li><a class='active noselect'>".$this->tabs[$i] ."</li>";
				}else{
					echo "<li><a href='".$this->tabs[$i].".php'>".$this->tabs[$i]."</a></li>";
				}
			}
		}
	}
	
	function get_html(){
		echo "
		<div class = 'navBar'>
			<nav>
				<ul>";
					 $this->get_tabs_html();
				echo "</ul>
			</nav>
		</div>";	
	}
}


?>