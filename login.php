<?php
	session_start();
	if(isset($_SESSION['username'])) {
  		echo "Your session is running " . $_SESSION['username'];
		header("location:index.php");
		exit();
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src='api/api.js'></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<!-- Google signin -->
	<script src="https://apis.google.com/js/platform.js" async defer></script>
	<meta name="google-signin-client_id" content="619527313666-fo2k03rjj7e8te5qd1ktvtkk718pr28h.apps.googleusercontent.com">

	<!--     Fonts and icons     -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

	<!-- CSS Files -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
	<link href="assets/css/material-kit.css" rel="stylesheet"/>

	<meta charset="utf-8" />
	<link rel="apple-touch-icon" sizes="76x76" href="img/logo.png">
	<link rel="icon" type="image/png" href="img/logo.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>Login</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

	<style>
		p {
			font-family: Arial;
			text-align: center;
		}

		#login {
			text-align: center;
			width: 100%;
			height: auto;
			margin: 100px auto;
		}

		.g-signin2 {
			display: table;
			margin: 0 auto;
		}
	</style>
</head>
<body class="signup-page">
	<script>
		var logout = <?php 
			if(isset($_GET['ref'])){  
				if($_GET['ref'] == "logout" && !isset($_SESSION['username'])){
					echo "true";
				} else {
					echo "false";
				}
			} else {
				echo "false";
			}	
		?>; 
		
	
		function onSignIn(googleUser) {
			console.log(logout);

			if(logout) {
				signOut();
			} else {
				
				var profile = googleUser.getBasicProfile();

				var id_token = googleUser.getAuthResponse().id_token;

				$.ajax({
					url: "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" + id_token,
					type: "GET",
					data: {},
					dataType: 'json',
					contentType: 'application/json',
					success: function(data) {
						var email = data["email"];
						var username = email.substring(0, email.indexOf("@"));
						var firstName = data["given_name"];
						var lastName = data["family_name"];		
						
						
						var emailUrl = email.replace(/.*@/, "");
						var username = email.substring(0, email.indexOf("@"));

						if(emailUrl == "scu.edu") {
	
							getUser(username, function(data) {
								console.log(data);
								if(data["message"] != null) {
									console.log("We gotta make a new user");
									$.ajax({
										url: "http://students.engr.scu.edu/~rjackson/Tea/api/user/create.php",
										type: "POST",
										data: JSON.stringify({
											username: username,
											firstName: firstName,
											lastName: lastName,
											email: email
										}),
										dataType: 'json',
										contentType: 'application/json',
										success: function(data) {
											createSession(data);
										}
									});
		
								} else {
									createSession(data['records'][0]);
								}
							});
						} else {
							alert("Please sign in with an @scu.edu email address");
							signOut();
						}
					}
				});
			}
		}

		function createSession(data) {
			$.ajax({
				url: "http://students.engr.scu.edu/~rjackson/Tea/api/session.php",
				type: "POST",
				data: JSON.stringify(data),
				dataType: 'json',
				contentType: 'application/json',
				success: function(data) {
					console.log(data);
					document.location.href = "http://students.engr.scu.edu/~rjackson/Tea/index.php";
				}
			});
		}

		function signOut() {
			logout = false;
			var auth2 = gapi.auth2.getAuthInstance();
			auth2.signOut().then(function () {
				console.log("User logged out");
			});
		}
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
						<a href="">
							Find Available
						</a>
    					</li>
					<li>
    						<a href="">
    							Calendar
    						</a>
    					</li>
        			</ul>
        		</div>
    		</div>
    	</nav>

    	<style>
    		.red {
    			background: linear-gradient(60deg, #b30739, #b30738) !important;
    		}

    		.btn-primary {
    			color: #b30739 !important;
    		}
	</style>

    	<div class="wrapper">
		<div class="header header-filter" style="background-image: url('assets/img/scu.jpg'); background-size: cover; background-position: top center;">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
						<br><br><br><br><br><br><br><br><br>
						<div class="card card-signup">
							<form class="form" method="" action="">
								<div class="red header header-primary text-center">
									<h4>Login</h4>
								</div>
								<p class="text-divider">Login with your SCU Email</p>
								<div class="content">
									<div class="btn btn-simple btn-primary btn-lg g-signin2" data-onsuccess="onSignIn" data-prompt="select_account"></div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
    	</div>
</body>
</html>
