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

	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>   
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<script src="https://apis.google.com/js/platform.js" async defer></script>
	<meta name="google-signin-client_id" content="619527313666-fo2k03rjj7e8te5qd1ktvtkk718pr28h.apps.googleusercontent.com">

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
<body>
	<div id="login">
		<p>Login</p><br>
		<div class="g-signin2" data-onsuccess="onSignIn"></div>
	</div>
	<script>
		var logout = <?php 
			if(isset($_GET['ref'])){  
				if($_GET['ref'] == "logout"){
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
						})
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
	
	<p id="output"><p>
	<script>
		/*console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
		console.log('Name: ' + profile.getName());
		console.log('Image URL: ' + profile.getImageUrl());
		console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.

		var email = profile.getEmail();
		var emailUrl = email.replace(/.*@/, "");
		var username = email.substring(0, email.indexOf("@"));

		if(emailUrl == "scu.edu") {
		getUser(username, function(data) {
		if(data["id"] == null) {

			var user = {
				firstName: profile.getName(),
				lastName: profile.getName(),
				email: email,
				username: username,
			};

			createUser(user, function(data) {
				alert("User succesfully created");
			})

			window.location = "http://localhost:8080/index.html";
		} else {
			/getEvents(data["id"], function(data) {
				document.getElementById("output").innerHTML = JSON.stringify(data);
			});

			window.location = "http://localhost:8080/index.html";
		}
		})
		} else {
		alert("Please sign in with an scu email address");
		signOut();
		}
		*/
	</script>
</body>
</html>
