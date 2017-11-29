<?php
	session_start();
	if(!isset($_SESSION['username'])) {
		header('location:login.php');
		exit();
		die();
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="assets/img/favicon.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>TA Emergency Availability</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

	<!--     Fonts and icons     -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

	<!-- CSS Files -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/material-kit.css" rel="stylesheet"/>

</head>

<body class="landing-page">
    <script>
	    window.onload = function(e){
		document.getElementById("availabilityButton").onclick = function() {
		    location.href = "http://students.engr.scu.edu/~rjackson/Tea/available.php";
		};

		document.getElementById("calendarButton").onclick = function() {
		    location.href = "http://students.engr.scu.edu/~rjackson/Tea/calendar.php";
		};
	    };
    </script>

    <nav class="navbar navbar-transparent navbar-absolute">
    	<div class="container">
		<div class="navbar-header">
        		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example">
            		<span class="sr-only">Toggle navigation</span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
        		</button>
        		<a class="navbar-brand" href="">TA Emergency Availability</a>
        	</div>

        	<div class="collapse navbar-collapse" id="navigation-example">
        		<ul class="nav navbar-nav navbar-right">
    				<li>
					<a href="available.php">
						Find Available
					</a>
    				</li>
    
				<li>
    					<a href="calendar.php">
    						Calendar
    					</a>
    				</li>

				<li>
    					<a href="logout.php">
						Logout <?php echo $_SESSION['username'];?>
					</a>
				</li>
        		</ul>
        	</div>
    	</div>
    </nav>

	<style>
		#firstContainer {
			padding-top: 20vh !important;
		}

		.top-buffer {
			margin-top: 20px;
		}

		a.nav {
			width: 100%;
			text-align: center;
		}
	</style>


	<div class="wrapper" style="background:#b30738">
		<div class="header header-filter">
		    <div id="firstContainer" class="container">
			<div class="row">
				<div class="col-md-12" style="text-align: center">
					<h1 class="title">Welcome to TA Emergency Availability</h1>
					<h4>What do you need to do?</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-5 col-md-offset-1 top-buffer">
					<div class="card card-bodys" style="text-align: center">
						<div class="content">
							<h3>Find Available TA</h3>
							<p>Input lab times to find an available TA</p>
						</div>
						<button id="availabilityButton" class="btn btn-primary btn-lrg" style="background-color:#b30738">Go</button>
					</div>
				</div>
				<div class="col-md-5 top-buffer">
					<div class="card card-body" style="text-align: center">
						<div class="content">
							<h3>Update my Schedule</h3>
							<p>Add, remove, edit or delete events from your calendar</p>
						</div>
						<button id="calendarButton" class="btn btn-primary btn-lrg" style="background-color:#b30738" >Go</button>
					</div>
				</div>
			</div>
		    </div>
		</div>
	</div>
</body>

	<!--   Core JS Files   -->
	<script src="assets/js/jquery.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="assets/js/material.min.js"></script>

	<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
	<script src="assets/js/nouislider.min.js" type="text/javascript"></script>

	<!--  Plugin for the Datepicker, full documentation here: http://www.eyecon.ro/bootstrap-datepicker/ -->
	<script src="assets/js/bootstrap-datepicker.js" type="text/javascript"></script>

	<!-- Control Center for Material Kit: activating the ripples, parallax effects, scripts from the example pages etc -->
	<script src="assets/js/material-kit.js" type="text/javascript"></script>

</html>
