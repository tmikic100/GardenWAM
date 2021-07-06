$(document).ready(function () {
	$('head').append("<link rel='stylesheet' type = 'text/css' href='../template/plantRemover/plantRemover.css'>");
		$('#removeButton').click(function(){
		var removeItem = $('#removeSelect').val();
		var port = $('#removeSelect>option[value='+removeItem+']').text().split(" ")[0].replace("P","");
		var plant = $('#removeSelect>option[value='+removeItem+']').text().split(" ")[1];
		alert(port+" "+plant);
		$.ajax({
			xhrFields: {
				withCredentials: true
			},
			headers: {
				'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
				'plant': plant
			},
			type: "DELETE",
			url: "../api/Website/delete.php"
		});
		$.ajax({
			xhrFields: {
				withCredentials: true
			},
			headers: {
				'Authorization': 'Basic ' + btoa('GardenWAM:xxxxxx'),
				'port': port-1
			},
			type: "DELETE",
			url: "../api/Plant/delete.php",
			success: function(){
				alert("Page will refresh");
				location.reload(true);
			},
			error: function(){
				alert("Couldn't remove item");
			}
		});
	});
});