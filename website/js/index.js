var names = new Array();
//gets all plants and checks for used ports each of used ports gets disabled and selects first free port
function disablePorts(){
	var count = 0;
	$.ajax({
		xhrFields: {
			withCredentials: true
		},
		headers: {
			'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx')
		},
		url: "../api/Plant/read.php",
		dataType: "json",
		async: false,
		success: function(data){
			$("#portSelect > option").each(function() {
				for(var i = 0; i < data.length; i++){
					if(this.value == data[i]["Port"]){
						$(this).attr("disabled", true);
						count++;
					}
				}
			});
			for(var i = 0; i < data.length; i++){
				names.push(data[i]["Name"]);
			}
		}
	});
	if(count == 4){
		$("#portSelect").attr("disabled", true);
	}else{
		$("#portSelect > option").each(function() {
			console.log($(this).attr("disabled") )
			if(typeof($(this).attr("disabled")) == "undefined"){
				$(this).attr("selected", true);
				return false;
			}
		});
	}
}

$(document).ready(function () {
	disablePorts();
	//when add was clicked it creates all necessary items for new website and refeshes the page
	$("#formsubmit").click( function(){
		var run = false;
		for(var i = 0; i < names.length; i++){
			if(names[i] == $("#fname").val()){
				alert("Name is already being used");
				return;
			}
		}
		$.ajax({
			xhrFields: {
				withCredentials: true
			},
			headers: {
				'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
				'plant':$("#fname").val(),
				'empty':0,
			},
			async: false,
			url: "../api/Data/read.php",
			success: function(data){
				console.log(data);
				if(typeof(data) == "undefined"){
					run = true;
				}
			}
		});
		$.ajax({
			xhrFields: {
				withCredentials: true
			},
			headers: {
				'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
				'plant':$("#fname").val()
			},
			type: "POST",
			url: "../api/Plant/create.php",
			dataType: "json",
			async: false,
			data: JSON.stringify({"Port":$("#portSelect").val()}),
			success: function(){
				console.log("yeet");
			}
		});
		if(run == true){
			$.ajax({
				xhrFields: {
					withCredentials: true
				},
				headers: {
					'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
					'plant':$("#fname").val(),
					'empty':0,
				},
				type: "POST",
				async: false,
				url: "../api/Data/create.php",
				success: function(){
					console.log("yeet");
				}
			});
		}
		$.ajax({
			type: "POST",
			url: "api/Website/create.php",
			dataType: "json",
			async: false,
			data: JSON.stringify({"name":$("#fname").val()}),
			success: function(){
			}
		});
		location.reload(true);
	});
});