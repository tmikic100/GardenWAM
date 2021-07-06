<?php
	$plant = "";
	foreach (getallheaders() as $name => $value) {
		if($name === "plant"){
			$plant = $value;
		}
	}
	if($plant === ""){
		die;
	}
	unlink("../../pages/".$plant.".php");
	echo("deleted website");
?>