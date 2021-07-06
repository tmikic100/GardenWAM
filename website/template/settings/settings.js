var phpValues = $('script[src*=settings]');
var plant = phpValues.attr('plant');
var settingNames = new Array();
//function updates information in sql setting new timestamp when setting was changed
function updateTimeStamp(name){
	$.ajax({
		xhrFields: {
			withCredentials: true
		},
		headers: {
			'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
			'plant': plant
		},
		type: "PUT",
		url: "../api/Information/update.php",
		dataType: "json",
		data: JSON.stringify({"Name":name}),
		success: function(){
			
		}
	});
}
//function which updates settings
function updateSettingsItem(){
	$.ajax({
		xhrFields: {
			withCredentials: true
		},
		headers: {
			'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
			'plant': plant
		},
		url: "../api/Settings/read.php",
		dataType: "json",
		async: false,
		success: function(data){
			var count = 0;
			for (var key in data) {
				// skip loop if the property is from prototype
				if (!data.hasOwnProperty(key)) continue;
				var obj = data[key];
				switch(obj["Type"]){
					case "Slider":
						$("#"+obj["Name"]+"Slider").val(parseInt(obj["Current"]));
						$("#"+obj["Name"]+"Value").text("Value: "+ obj["Current"] +" "+obj["Unit"]);
						console.log(obj["Unit"]);
						break;
					case "Select": 
						if(obj["Dependency"] == "none"){
							$("#"+obj["Name"]+"Select").val(obj["Current"]);
						}else{
							//find on which setting is this setting is depended on
							for(i = 0; i < data.length; i++){
								if(data[i]["Name"] == obj["Dependency"])
									break;
							}
							//get current value of depended setting
							var options = $("#"+obj["Dependency"]+"Select"+">option[value='"+data[i]["Current"]+"']");
							option = options.text();
							//get new options depending on current state of depnded setting
							var newOptions = obj["Data"][option];
							//replace all options with new ones
							var $el = $("#"+obj["Name"]+"Select");
							$el.empty(); // remove old options
							$.each(newOptions, function(key,value) {
							$el.append($("<option></option>")
								.attr("value", value).text(key));
							});
							$el.val(obj["Current"]);
						}
						break;
					
				}
				//create a local settings array which has all important data and when there is dependecy then define which setting is slave and which is the master
				if(obj["Dependency"] == "none" && typeof settingNames[count] == 'undefined'){
					settingNames[count]={"Name":obj["Name"],"SettingGroup":obj["SettingGroup"], "Type":obj["Type"], "Unit":obj["Unit"],"Dependency":"none","DependedBy":"none"};
				}else if(typeof settingNames[count] == 'undefined'){
					settingNames[count]={"Name":obj["Name"],"SettingGroup":obj["SettingGroup"], "Type":obj["Type"], "Unit":obj["Unit"],"Data":obj["Data"],"Dependency":"Salve_"+obj["Dependency"], "DependedOn":i};
					settingNames[i]={"Name":data[i]["Name"],"SettingGroup":obj["SettingGroup"], "Type":data[i]["Type"], "Unit":data[i]["Unit"],"Dependency":"Master_"+obj["Name"], "DependedBy":count};
				}
				count++;
			}
		}
	});
}
/*
 *function which gets first item of array
 *input array data
 *output item first element of the array
 */
function getFirst(data) {
  for (var prop in data)
    if (data.propertyIsEnumerable(prop))
      return prop;
}
$(document).ready(function () {
	$('head').append("<link rel='stylesheet' type = 'text/css' href='../template/settings/settings.css'>");
	var interval;
	var range;
	var language;
	var mode;
	var period;
	var lowMoistureLevel;
	var wateringTime;
	//expansion panel logic
	$(".expansionPanelBody").hide();
	$(".expansionPanelHeader").click(function(){
		if($(this).parent().height() === 22){
			$(this).parent().children(".expansionPanelBody").show();
		}else{
			$(this).parent().children(".expansionPanelBody").hide();
		}
	});
	updateSettingsItem();
	for(var i = 0; i < settingNames.length; i++){
		var name = settingNames[i]["Name"];
		var	unit = settingNames[i]["Unit"];
		switch(settingNames[i]["Type"]){
			case "Slider":
				var stringSlider = "#"+name+"Slider";
				//when slider is moving then set current value
				$(stringSlider).on('input', function(){
					var i;
					var id = this.id.replace("Slider","");
					for(i = 0; i < settingNames.length; i++){
						if(settingNames[i]["Name"] === id)
							break;
					}
					var stringValue = "#"+this.id.replace("Slider","")+"Value";
					$(stringValue).text("Value: "+this.value+" "+settingNames[i]["Unit"]);
				});
				//when slider is stopped update current value to mysql database
				$(stringSlider).on('change', function(){
					var i;
					var id = this.id.replace("Slider","");
					//this is forgeting id of current setting so you can update it
					for(i = 0; i < settingNames.length; i++){
						if(settingNames[i]["Name"] === id)
							break;
					}
					$.ajax({
						xhrFields: {
							withCredentials: true
						},
						headers: {
							'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
							'plant': plant
						},
						type: "PUT",
						url: "../api/Settings/update.php",
						dataType: "json",
						data: JSON.stringify({"Name":id, "Current":this.value}),
						success: function(){
							
						}
					});
					
					updateTimeStamp(settingNames[i]["SettingGroup"]);
					updateSettingsInSettings(settingNames[i]["Name"], this.value);
				});
				break;
			case "Select": 
				var stringSelect = "#"+name+"Select";
				$(stringSelect).on('change',function(){
					var id = this.id.replace("Select","");
					var data = new Array();
					data.push(this.value);
					stringSelect = "#"+id+"Select";
					var i = 0;
					//this is forgeting id of current setting so you can update it
					for(i = 0; i < settingNames.length; i++){
						if(settingNames[i]["Name"] === id)
							break;
					}
					if(settingNames[i]["Dependency"] != "none"){
						//checking if setting that is changed is master to some setting
						if(settingNames[i]["Dependency"].split("_")[0] == "Master"){
							var options = $(stringSelect+">option[value='"+this.value+"']");
							//getting slave id so I can update it
							var dependencyString = "#"+settingNames[i]["Dependency"].split("_")[1]+"Select";
							option = options.text();
							//get new options depending on current state of depnded setting
							var newOptions = settingNames[settingNames[i]["DependedBy"]]["Data"][option];
							//replace all options with new ones
							var $el = $(dependencyString);
							$el.empty(); // remove old options
							var first = true;
							$.each(newOptions, function(key,value) {
								$el.append($("<option></option>")
								.attr("value", value).text(key));
							});
							for(var key in settingNames[settingNames[i]["DependedBy"]]["Data"][option]) {
								data.push(settingNames[settingNames[i]["DependedBy"]]["Data"][option][key]);
								break;
							}
							$.ajax({
								xhrFields: {
									withCredentials: true
								},
								headers: {
									'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
									'plant': plant
								},
								type: "PUT",
								url: "../api/Settings/update.php",
								dataType: "json",
								data: JSON.stringify({"Name":settingNames[settingNames[i]["DependedBy"]]["Name"], "Current":data[1]}),
								success: function(){
									
								}
							});
						}
					}
					$.ajax({
						xhrFields: {
							withCredentials: true
						},
						headers: {
							'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
							'plant': plant
						},
						type: "PUT",
						url: "../api/Settings/update.php",
						dataType: "json",
						data: JSON.stringify({"Name":id, "Current":this.value}),
						success: function(){
							
						}
					});
					
					updateTimeStamp(settingNames[i]["SettingGroup"]);
					updateSettingsInSettings(settingNames[i]["Name"], data);
				});
				break;
		
		}
	}
});