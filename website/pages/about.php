<!DOCTYPE html>
<html>
	<?php include '../template/head.php'; 
	$header = new head("test");
	$header->get_html_pages();?>
	<body>
		<?php include '../template/navBar.php';
			$navBar = new navBar();
			$navBar->get_html();?>
	</body>
</html>