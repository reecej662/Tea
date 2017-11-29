<?php
	session_start();
	if(!isset($_SESSION['username'])) {
		header('location:login.php');
		exit();
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' href='../Tea/dist/fullcalendar/fullcalendar.css' />
	<script src='dist/lib/jquery/dist/jquery.min.js'></script>
	<script src='dist/lib/moment/min/moment.min.js'></script>
	<script src='api/api.js'></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<!--<link rel="stylesheet" href="../Tea/css/style.css">-->
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
	<script src='../Tea/dist/fullcalendar/fullcalendar.js'></script>

	<script src="https://apis.google.com/js/platform.js"></script>
	<meta name="google-signin-client_id" content="619527313666-fo2k03rjj7e8te5qd1ktvtkk718pr28h.apps.googleusercontent.com">

	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="img/logo.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>TA Emergency Availability</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

	<!--     Fonts and icons     -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

	<!-- CSS Files -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

	<script>
		function getAvailable() {
			var start = $('#addStart').val();
			var end = $('#addEnd').val();

			available(start, end, function(data) {
				
				tableString = "<thead><tr><td>Name</td><td>Email</td></tr></thead><tbody>";	
			
				for(index in data) {
					var person = data[index];
					if(person['id'] != <?php echo $_SESSION['id']?>){
						tableString += '<tr><td>' + person['firstName'] + " " + person['lastName'] + '</td><td>' + person['email'] + '</td></tr>'
					}
				}

				tableString += "</tbody>";

				$('#resultLable').removeAttr("hidden");
				$('#result').html(tableString);
			});
		}
	</script>
</head>
<body>
<body style="background-color: #fff">
	<nav class="navbar navbar-transparent navbar-absolute" style="background-color: #b30739;">
    		<div class="container">
        	        <div class="navbar-header">
        			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example">
				    <span class="sr-only">Toggle navigation</span>
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
				</button>
        		<a class="navbar-brand" style="color:white;" href="index.php">Find Available TAs</a>
        	</div>

        	<div class="collapse navbar-collapse" id="navigation-example">
        		<ul class="nav navbar-nav navbar-right">
				<li>
					<a style="color:white;" href="http://students.engr.scu.edu/~rjackson/Tea/index.php">
						Home
					</a>
				</li>	
    				<li>
					<a style="color:white;" href="">
						Find Available
					</a>
    				</li>
				<li>
    					<a style="color:white;" href="http://students.engr.scu.edu/~rjackson/Tea/calendar.php">
    						Calendar
    					</a>
    				</li>
    				<li>
    					<a style="color:white;" href="http://students.engr.scu.edu/~rjackson/Tea/logout.php">Logout <?php echo $_SESSION['username'];?></a>
        			</li>
			</ul>
        	</div>
    	</div>
    </nav>

	<style>
		.space { 
			margin-top: 20px;
		}

		.left {
			float: left;
		}

		.right {
			float: right;
		}
		.row {
			height: 43px;
		}
	</style>
	<div class="container">
		<div class="row" style="height:60px;">
			<div class="col-sm-3">
				<h3>Search by Lab Time</h3>
				<!--<p>Select the clock icon to the right of the forms below to select lab date and times</p>-->
			</div>
			<div id="resultLable" hidden=true class="col-sm-9">
				<h3>Results</h3>
			</div>
		</div>
		<div class="row">
			<div class='col-sm-3'>
				<div class="container">
					<p style="margin-top: 10px;">Enter the lab start time:</p>
				</div>
			</div>
	        	<div class='col-sm-9 right' style="position: relative;">
				<table class="table table-striped" id="result"></table>
			</div>
			
	        	<script type="text/javascript">
	            		$(function () {
	                		$('#datetimepicker1').datetimepicker();
	            		});
	        	</script>
		</div>
		<div class="row">	
			<div class='col-sm-3'>
	            		<div class='input-group date' id='datetimepicker1'>
	                		<input type='text' class="form-control" id='addStart'/>
	                		<span class="input-group-addon">
	                    			<span class="glyphicon glyphicon-time"></span>
	                		</span>
	            		</div>
	        	</div>
		</div>
		<div class="row space">
			<div class='col-sm-3'>
				<div class="container">
					<p style="margin-top: 10px;">Enter the lab end time:</p>
				</div>
	   		</div>
	   	</div>
	   	<div class="row">
	        <div class='col-sm-3'>
	            <div class='input-group date' id='datetimepicker2'>
	                <input type='text' class="form-control" id='addEnd'/>
	                <span class="input-group-addon">
	                    <span class="glyphicon glyphicon-time"></span>
	                </span>
	            </div>
	        </div>
	        <script type="text/javascript">
	            $(function () {
	                $('#datetimepicker2').datetimepicker();
	            });
	        </script>
		</div>
		<div class="row space">
			<div class="col-sm-3" style="padding-left: 30px;">
				<button id='availabilityButton' class="btn btn-primary btn-lrg" style="background-color:#b30738" onclick='getAvailable()'>Search</button>
			</div>
		</div>
	</div>
</body>
</html>
