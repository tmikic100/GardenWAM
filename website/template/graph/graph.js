var phpValues = $('script[src*=graph]');
var vars = phpValues.attr('vars').replace(/\[|\]|"/gi,"").split(",");
var names = phpValues.attr('names').replace(/\[|\]|"/gi,"").split(",");
var graphPlant = phpValues.attr('plant');
var AirTemp = [[0,0]];
var SoilTemp = [[0,0]];
var Humidity = [[0,0]];
var Moisture = [[0,0]];
var interval;
var range;
var tempOptions;
var humdOptions;
var tempData;
var humdData;
var lastTimeStampSettings = 0;
var lastTimeStampData = 0;
var tempChart;
var humdChart;
var updateTimeout;
/*
 *function for getting minimum value of a array
 *input array arr
 *output value min
 */
function getMin(arr){
	var len = arr.length;
	var min = Infinity;
	while (len--) {
		if (arr[len][1] < min) {
			min = arr[len][1];
		}
	}
  return min;
}
/*
 *function for getting maxium value of a array
 *input array arr
 *output value max
 */
function getMax(arr){
	var len = arr.length;
	var max = -Infinity;
	while (len--) {
		if (arr[len][1] > max) {
			max = arr[len][1];
		}
	}
	return max;
}
/*
 *function for getting average value of a array
 *input array arr
 *output value avg
 */
function getAvg(arr){
	var len = arr.length;
	var avg = 0;
	while (len--) {
		avg += arr[len][1];
	}
	return parseFloat(avg/arr.length).toFixed(2);
}
/*
 *function for getting last time of a array
 *input array arr
 *output time min
 */
function getMinTime(arr){
	var len = arr.length;
	var min = Infinity;
	while (len--) {
		if (arr[len][0] < min) {
			min = new Date(arr[len][0]).getMilliseconds();
		}
	}
  return min;
}
/*
 *function for getting first time of a array
 *input array arr
 *output value max
 */
function getMaxTime(arr){
	var len = arr.length;
	var max = -Infinity;
	while (len--) {
		if (arr[len][0] > max) {
			max = new Date(arr[len][0]).getMilliseconds();
		}
	}
	return max;
}
//function for getting initial block of data depended on set range
function getDataInital(){	
	AirTemp.shift();
	SoilTemp.shift();
	Humidity.shift();
	Moisture.shift();
	$.ajax({
		xhrFields: {
			withCredentials: true
		},
		headers: {
			'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
			'plant': graphPlant,
			'amount': range/10
		},
		url: "../../api/Data/read.php",
		dataType: "json",
		async: false,
		success: function(data){
			for(var i = 0; i < data.length; i++){
				Moisture.push([
					new Date(data[i]["TimeStamp"]),
					parseFloat(data[i]["Moisture"])
				]);
				Humidity.push([
					new Date(data[i]["TimeStamp"]),
					parseFloat(data[i]["Humidity"])
				]);
				SoilTemp.push([
					new Date(data[i]["TimeStamp"]),
					parseFloat(data[i]["SoilTemp"])
				]);
				AirTemp.push([
					new Date(data[i]["TimeStamp"]),
					parseFloat(data[i]["AirTemp"])
				]);
			}
		}
	});
}
//function for getting lastest data to arrays
function getData(){
	AirTemp.shift();
	SoilTemp.shift();
	Humidity.shift();
	Moisture.shift();
	$.ajax({
		xhrFields: {
			withCredentials: true
		},
		headers: {
			'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
			'plant': graphPlant
		},
		url: "../../api/Data/read.php",
		dataType: "json",
		async: false,
		success: function(data){
			Moisture.push([
				new Date(data["TimeStamp"]),
				parseFloat(data["Moisture"])
			]);
			Humidity.push([
				new Date(data["TimeStamp"]),
				parseFloat(data["Humidity"])
			]);
			SoilTemp.push([
				new Date(data["TimeStamp"]),
				parseFloat(data["SoilTemp"])
			]);
			AirTemp.push([
				new Date(data["TimeStamp"]),
				parseFloat(data["AirTemp"])
			]);
		}
	});
}
var firstRun = true;
//function called by settings template to update settings for graphs
function updateSettingsInSettings(name, data){
	switch(name){
		case "Interval":
			interval = data[0];
			humdOptions = {
				yaxis:{
					min:0, 
					max:100,
					tickFormatter:function(val){return val+"%";},
					color: '#FFB515'
				},
				xaxis:{
					tickSize: [interval/120, "minute"],
					minTickSize: [1, "minute"],
					label: "Time",
					color: '#FFB515',
					mode: 'time', 
					timeformat:'%y/%d/%m %H:%M'
				},
				grid:{
					color: '#FFFFFF',
					tickColor: '#FFFFFF'
				}
			};
			tempOptions = {
				yaxis:{
					min:-10, 
					max:40,
					tickFormatter:function(val){return val+"°C";},
					color: '#FFB515'
				},
				xaxis:{
					tickSize: [interval/120, "minute"],
					minTickSize: [1, "minute"],
					color: '#FFB515',
					mode: 'time', 
					timeformat:'%y/%d/%m %H:%M'
					
				},
				grid:{
					color: '#FFFFFF',
					tickColor: '#FFFFFF',
					margin: { top:50 }
				}
			};
			break;
		case "Range":
			range = data[0];
			AirTemp = [];
			SoilTemp = [];
			Humidity = [];
			Moisture = [];
			getDataInital();
			tempData = [{
					data: AirTemp, 
					label: vars[0].replace("_"," ").replace("Temp","Temperature"),
					lines:{
						show:true
					}
				},{
					data: SoilTemp,
					label: vars[1].replace("_"," ").replace("Temp","Temperature"),
					lines:{
						show:true
					}
				}];
			humdData = [{
					data: Humidity,
					label: vars[2],
					lines:{
						show:true
					}
				},{
					data: Moisture, 
					label: vars[3],
					lines:{
						show:true
					}
			}];
			interval = data[1];
			humdOptions = {
				yaxis:{
					min:0, 
					max:100,
					tickFormatter:function(val){return val+"%";},
					color: '#FFB515'
				},
				xaxis:{
					tickSize: [interval/120, "minute"],
					minTickSize: [1, "minute"],
					label: "Time",
					color: '#FFB515',
					mode: 'time', 
					timeformat:'%y/%d/%m %H:%M'
				},
				grid:{
					color: '#FFFFFF',
					tickColor: '#FFFFFF'
				}
			};
			tempOptions = {
				yaxis:{
					min:-10, 
					max:40,
					tickFormatter:function(val){return val+"°C";},
					color: '#FFB515'
				},
				xaxis:{
					tickSize: [interval/120, "minute"],
					minTickSize: [1, "minute"],
					color: '#FFB515',
					mode: 'time', 
					timeformat:'%y/%d/%m %H:%M'
					
				},
				grid:{
					color: '#FFFFFF',
					tickColor: '#FFFFFF',
					margin: { top:50 }
				}
			};
			break;
	}
	if(firstRun == false){
		$.plot($("#"+names[0]+"Chart"),tempData,tempOptions);
		$.plot($("#"+names[1]+"Chart"),humdData,humdOptions);
	}
}
//function to get current settings
function getSettings(){
	$.ajax({
		xhrFields: {
			withCredentials: true
		},
		headers: {
			'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
			'plant': graphPlant
		},
		url: "../../api/Settings/read.php",
		dataType: "json",
		async: false,
		success: function(data){
			for(var i = 0; i < data.length; i++){
				switch(data[i]["Name"]){
					case "Interval":
						if(interval != data[i]["Current"]){
							interval = data[i]["Current"];
							humdOptions = {
								yaxis:{
									min:0, 
									max:100,
									tickFormatter:function(val){return val+"%";},
									color: '#FFB515'
								},
								xaxis:{
									tickSize: [interval/120, "minute"],
									minTickSize: [1, "minute"],
									label: "Time",
									color: '#FFB515',
									mode: 'time', 
									timeformat:'%y/%d/%m %H:%M'
								},
								grid:{
									color: '#FFFFFF',
									tickColor: '#FFFFFF'
								}
							};
							tempOptions = {
								yaxis:{
									min:-10, 
									max:40,
									tickFormatter:function(val){return val+"°C";},
									color: '#FFB515'
								},
								xaxis:{
									tickSize: [interval/120, "minute"],
									minTickSize: [1, "minute"],
									color: '#FFB515',
									mode: 'time', 
									timeformat:'%y/%d/%m %H:%M'
									
								},
								grid:{
									color: '#FFFFFF',
									tickColor: '#FFFFFF',
									margin: { top:50 }
								}
							};
						}
						break;
					case "Range":
						if(range != data[i]["Current"]){
							range = data[i]["Current"];
							AirTemp = [];
							SoilTemp = [];
							Humidity = [];
							Moisture = [];
							getDataInital();
							tempData = [{
								data: AirTemp, 
								label: vars[0].replace("_"," ").replace("Temp","Temperature"),
								lines:{
									show:true
								}
							},{
								data: SoilTemp,
								label: vars[1].replace("_"," ").replace("Temp","Temperature"),
								lines:{
									show:true
								}
							}];
							humdData = [{
								data: Humidity,
								label: vars[2],
								lines:{
									show:true
								}
							},{
								data: Moisture, 
								label: vars[3],
								lines:{
									show:true
								}
							}];
						}
						break;
				}
			}
		}
	});
	if(firstRun == false){
		$.plot($("#"+names[0]+"Chart"),tempData,tempOptions);
		$.plot($("#"+names[1]+"Chart"),humdData,humdOptions);
	}
}
//function to update min,max and avg for each type of value
function updateStatistics(){
	var Temp=[[0,0]];
	Temp.shift();
	Temp.push(AirTemp);
	Temp.push(SoilTemp);
	var Humd=[[0,0]];
	Humd.shift();
	Humd.push(Humidity);
	Humd.push(Moisture);
	for(var i = 0; i < 2; i++){
		$("#"+vars[i]+"Max").text("Max value = " + getMax(Temp[i]) + "°C");
		$("#"+vars[i]+"Min").text("Min value = " + getMin(Temp[i]) + "°C");
		$("#"+vars[i]+"Avg").text("Avg value = " + getAvg(Temp[i]) + "°C");
	}
	for(var i = 0; i < 2; i++){
		$("#"+vars[i+2]+"Max").text("Max value = " + getMax(Humd[i]) + "%");
		$("#"+vars[i+2]+"Min").text("Min value = " + getMin(Humd[i]) + "%");
		$("#"+vars[i+2]+"Avg").text("Avg value = " + getAvg(Humd[i]) + "%");
	}
}
/*
 *function to check if there is some change in data or settings
 *if there is change value for one of those options it updates
 *the graph with new data values or new setting values
 */
function update(){
	$.ajax({
		xhrFields: {
			withCredentials: true
		},
		headers: {
			'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
			'plant': graphPlant
		},
		url: "../../api/Information/read.php",
		dataType: "json",
		async: false,
		success: function(data){
			var curTimeStampSettings = 0;
			var curTimeStampData = 0;
			for(var i = 0; i < data.length;i++){
				switch(data[i]["Name"]){
					case "Website":
						curTimeStampSettings = data[i]["Time"];
						break;
					case "Data":
						curTimeStampData = data[i]["Time"];
						break;
				}
			}
			if(curTimeStampSettings != lastTimeStampSettings){
				getSettings();
				lastTimeStampSettings = curTimeStampSettings;
			}
			if(curTimeStampData != lastTimeStampData && firstRun == false){
				getData();
				tempData = [{
					data: AirTemp, 
					label: vars[0].replace("_"," ").replace("Temp","Temperature"),
					lines:{
						show:true
					}
				},{
					data: SoilTemp,
					label: vars[1].replace("_"," ").replace("Temp","Temperature"),
					lines:{
						show:true
					}
				}];
				humdData = [{
					data: Humidity,
					label: vars[2],
					lines:{
						show:true
					}
				},{
					data: Moisture, 
					label: vars[3],
					lines:{
						show:true
					}
				}];
				$.plot($("#"+names[0]+"Chart"),tempData,tempOptions);
				$.plot($("#"+names[1]+"Chart"),humdData,humdOptions);
				lastTimeStampData = curTimeStampData;
			}
			firstRun = false;
		}
	});
	updateStatistics();
}
//debuging and showcase feature so I can insert random data instead of rely on real world data
function addData(){
	$.ajax({
		xhrFields: {
			withCredentials: true
		},
		headers: {
			'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
			'plant': plant
		},
		type: "POST",
		url: "../api/Data/create.php",
		dataType: "json",
		data: JSON.stringify({"AirTemp":Math.floor(Math.random() * 50)-10,"SoilTemp":Math.floor(Math.random() * 50)-10,"Moisture":Math.floor(Math.random() * 100),"Humidity":Math.floor(Math.random() * 100)}),
		success: function(){
			
		}
	});
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
		data: JSON.stringify({"Name":"Data"}),
		success: function(){
			
		}
	});
	
}
//when document is loaded add css and start update on specified interval 
$(document).ready(function () {
	$('head').append("<link rel='stylesheet' type = 'text/css' href='../template/graph/graph.css'>");
	update();
	$.plot($("#"+names[0]+"Chart"),tempData,tempOptions);
	$.plot($("#"+names[1]+"Chart"),humdData,humdOptions);
	addData();
	setInterval(update, 5000);
	setInterval(addData, 5000);
});