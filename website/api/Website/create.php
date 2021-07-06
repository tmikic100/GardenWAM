<?php
	$content = trim(file_get_contents("php://input"));
	$decoded = json_decode($content, true);
	$name = $decoded['name'];
	//if name is set then it creates a website based on this template
	if(isset($decoded['name']) === true){
		$newsite = fopen("../../pages/".$name.".php","w");
		$html =
			"<!DOCTYPE html>
	<html>
		<?php include '../template/head.php'; 
		\u{0024}header = new head('$name Page');
		\u{0024}header->get_html_pages();?>
		<body>
			<h1>$name Page</h1>
			<div class = 'globalContiner'>
				<?php include '../template/graph/graph.php';
				\u{0024}graph = new graph('$name');
				\u{0024}graph->get_html();?>
				<?php include '../template/settings/settings.php';
				\u{0024}context  = stream_context_create(\u{0024}options);
				\u{0024}result = file_get_contents(\u{0024}url, false, \u{0024}context);
				if (\u{0024}result === FALSE){
							http_response_code(400);
							die('Something went wrong');
				}
				\u{0024}decoded = json_decode(\u{0024}result, true);
				\u{0024}found = -1;
				\u{0024}sets_amount = 1;
				for(\u{0024}i = 0; \u{0024}i < sizeof(\u{0024}decoded); \u{0024}i++){
					\u{0024}data = array('name'=>\u{0024}decoded[\u{0024}i]['Name'],'data'=>\u{0024}decoded[\u{0024}i]['Data'],'dependency'=>\u{0024}decoded[\u{0024}i]['Dependency']);
					if(\u{0024}i == 0){
						\u{0024}sets[] = new expansionPanel(\u{0024}decoded[\u{0024}i]['SettingGroup']);
						\u{0024}sets[0]->add_Setting(new settingsObject(\u{0024}decoded[\u{0024}i]['Type'],\u{0024}data),\u{0024}decoded[\u{0024}i]['Name']);
					}else{
						for(\u{0024}j = 0; \u{0024}j < sizeof(\u{0024}sets); \u{0024}j++){
							if(\u{0024}sets[\u{0024}j]->get_name() === \u{0024}decoded[\u{0024}i]['SettingGroup']){
								\u{0024}found = \u{0024}j;
							}
						}
						if(\u{0024}found === -1){
							\u{0024}sets[] = new expansionPanel(\u{0024}decoded[\u{0024}i]['SettingGroup']);
							\u{0024}sets[\u{0024}sets_amount]->add_Setting(new settingsObject(\u{0024}decoded[\u{0024}i]['Type'],\u{0024}data),\u{0024}decoded[\u{0024}i]['Name']);
							\u{0024}sets_amount++;
						}else{
							\u{0024}sets[\u{0024}found]->add_Setting(new settingsObject(\u{0024}decoded[\u{0024}i]['Type'],\u{0024}data),\u{0024}decoded[\u{0024}i]['Name']);
							\u{0024}found = -1;
						}
					}
				}
				\u{0024}settings = new settings('$name');
				for(\u{0024}i = 0; \u{0024}i < sizeof(\u{0024}sets); \u{0024}i++){
					\u{0024}settings->add_expansionPanel(\u{0024}sets[\u{0024}i], \u{0024}sets[\u{0024}i]->get_name());
				}
				\u{0024}settings->get_HTML();			
				?>
			</div>
			
			<?php include '../template/navBar.php';
				\u{0024}navBar = new navBar();
				\u{0024}navBar->get_html();?>
			
		</body>
	</html>" ;
		fwrite($newsite, $html);
		fclose($newsite);
	}
?>