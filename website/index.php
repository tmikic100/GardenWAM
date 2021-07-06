<!DOCTYPE html>
<html>
	<?php include 'template/head.php'; 
	$header = new head("GardenWAM");
	$header->get_html();?>
	<body>
		<h1>Garden Watering and Monitoring</h1>
		<div class = "globalContiner">
			<div class = "plantContiner">
				<div class = "plantItem"> 
					<div class = "plantCreationContiner">
						<div class = "plantCreationItem">
							<h4>Plant name:</h4> <input type="text" id="fname">
						</div>
						<div class = "plantCreationItem">
							<h4>Port:</h4> <select id="portSelect">
								<option value="0">P1</option>
								<option value="1">P2</option>
								<option value="2">P3</option>
								<option value="3">P4</option>
							</select>
						</div>
						<input id="formsubmit" type="button" value="Add" style="width: 40px; height:20px;">
					</div>
				</div>
				<div class="plantItem">
					<?php include 'template/plantRemover/plantRemover.php';
					$remover = new plantRemover();
					$remover->get_HTML();?>
				</div>
			</div>
			<?php include 'template/plantSummary/plantSummary.php';
			$plant = new plantSummary();
			$plant->get_HTML();?>
		</div>
		<?php include 'template/navBar.php';
			$navBar = new navBar();
			$navBar->get_html();?>
	</body>
</html>