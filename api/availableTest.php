<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src='../dist/lib/moment/min/moment.min.js'></script>
	<script src="api.js"></script>
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
 	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body>
	<style>
		body{
			padding: 10px;
		}
	</style>
	
	<input id="start" type="text" value="2017-11-15 12:00:00"></input>
	<input id="end" type="text" value="2017-11-15 16:00:00"></input>
	<button onclick="getAvailable()">Get Available TA's</button>

	<br><br>

	<table class="table table-striped" id="result"></table>

	<script>
		$(document).ready(function() {
			getAvailable();
		});

		function getAvailable() {
			var start = $('#start').val();
			var end = $('#end').val();

			available(start, end, function(data) {
				
				tableString = "<thead><tr><td>Available</td><td>Email</td></tr></thead><tbody>";	
			
				for(index in data) {
					var person = data[index];
					tableString += '<tr><td>' + person['firstName'] + " " + person['lastName'] + '</td><td>' + person['email'] + '</td></tr>'
				}

				tableString += "</tbody>";

				$('#result').html(tableString);
			});
		}
	</script>
</body>
</html>
