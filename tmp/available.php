<?php
	session_start();
	if(isset($_SESSION['username'])) {
  	//	echo $_SESSION['username'];
			$username = $_SESSION['username'];
	} else {
		header('location:login.php');
		exit();
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' href='dist/fullcalendar/fullcalendar.css' />
	<script src='dist/lib/jquery/dist/jquery.min.js'></script>
	<script src='dist/lib/moment/min/moment.min.js'></script>
	<script src='api/api.js'></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<link rel="stylesheet" href="css/style.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
	<script src='dist/fullcalendar/fullcalendar.js'></script>

	<script src="https://apis.google.com/js/platform.js"></script>
	<meta name="google-signin-client_id" content="619527313666-fo2k03rjj7e8te5qd1ktvtkk718pr28h.apps.googleusercontent.com">

	<script>
		var events = [];
		var userId = <?php echo $_SESSION['id']?>;
		var username = "<?php echo $_SESSION['username']?>";

		function initClient(callback) {
		    getSchedule(userId);
		};

		$(document).ready(function() {
			getEvents(userId, function (data) {
				events = data["events"];

				if(events) {
					$('#calendar').fullCalendar('removeEvents');
					$('#calendar').fullCalendar('renderEvents', events, true);
				}
			})
		});

		function refreshCalendar() {
			getEvents(userId, function (data) {
				console.log("Getting here");
				events = data["events"];

				$('#calendar').fullCalendar('removeEvents');
				$('#calendar').fullCalendar('renderEvents', events, true);
			});
		}

		function makeEvents(events) {
			var eventArray = [];

			for(index in events) {
				eventArray.push({
					eventId: events[index]["eventId"],
					userId: events[index]["userId"],
					title: events[index]["title"],
					start: moment(events[index]["start"]).format(),
					end: moment(events[index]["end"]).format()
				})
			}

			console.log(eventArray);
			return eventArray;
		}

		function getSchedule(userId) {
			events = [];
			userId = userId;

			getEvents(userId, function(data) {
				events = data;
			});
		}

		function addEvent(userId, date) {
			var eventTitle = document.getElementById("addTitle").value;
			var start = convertToMilitary( document.getElementById("addStart").value ); // HH:MM AM/PM format
			var end = convertToMilitary( document.getElementById("addEnd").value );
			var startTime = moment(date+'T'+start+'-08:00').format("YYYY-MM-DD HH:mm:ss");
			var endTime = moment(date+'T'+end+'-08:00').format("YYYY-MM-DD HH:mm:ss");

			var event = {
				userId: userId,
				eventId: 1,
				title: eventTitle,
				start: startTime,
				end: endTime
			}

			createEvent(event, function(data) {
				refreshCalendar();
			});
		}

		function editEvent(calEvent, userId) {
			var eventTitle = document.getElementById("editTitle").value;
			var start = convertToMilitary( document.getElementById("editStart").value ); // HH:MM AM/PM format
			var end = convertToMilitary( document.getElementById("editEnd").value );
			var date = $.fullCalendar.moment(calEvent.start).format("YYYY-MM-DD")
			var startTime = moment(date+'T'+start+'-08:00').format("YYYY-MM-DD HH:mm:ss");
			var endTime = moment(date+'T'+end+'-08:00').format("YYYY-MM-DD HH:mm:ss");

			var event = {
				id: calEvent["id"],
				userId: userId,
				eventId: 1,
				title: eventTitle,
				start: startTime,
				end: endTime,
			}

			updateEvent(event, function(data) {
				refreshCalendar();
			})
		}

		function deleteEvent(calEvent, userId) {
			console.log(calEvent);

			var id = calEvent["id"];

			deleteEventBackend(id, function (data) {
				refreshCalendar();
			})
		}

		function testGetAvailable() {
			var format = 'MMM Do YYYY, h:mm a';

			$.ajax({
				url: window.location.href + "tea/available",
				type: "POST",
				data: JSON.stringify({
					userId: userId,
					start: '2017-10-23T19:00:00+07:00',
					end: '2017-10-23T20:00:00+07:00'
				}),
				dataType: 'json',
				contentType: 'application/json',
				success: function(data) {
					var availableString = "";
					data.forEach(function (item) {
						var start = $.fullCalendar.moment(item.availabilityStart).utc().format(format);
						var end = $.fullCalendar.moment(item.availabilityEnd).utc().format(format);
						availableString += "<br>" + item.userId + " is available from " + start + " to " + end;
					});

					$("#availabilityResults").html(availableString);
				}
			});
		}

		function hidedp() {
			$('#datetimepicker3').datetimepicker().hide();
		}

		function convertToMilitary(string) {
			var noSpaces = string.replace(/\s/g,'');
			var size = noSpaces.length;
			var AMPM = noSpaces.substr(size-2,2);
			var hours;
			var minutes;
			var hoursStr;
			if (AMPM == 'PM') {
				if (size == 7 ) {
					hours = Number(noSpaces.substr(0,2));
					minutes = noSpaces.substr(3,2);
					if( hours != 12 )
						hours += 12;
					hoursStr = hours.toString()
				} else {
					hours = Number(noSpaces.charAt(0));
					minutes = noSpaces.substr(2,2);
					hours += 12;
					hoursStr = hours.toString();
				}
			} else {
				if (size == 7 ) {
					hours = Number(noSpaces.substr(0,2));
					minutes = noSpaces.substr(3,2);
					if( hours == 12 ) {
						hoursStr = '00';
					} else {
						hoursStr = hours.toString()
					}
				} else {
					hours = Number(noSpaces.charAt(0));
					minutes = noSpaces.substr(2,2);
					hoursStr = '0' + hours.toString();
				}
			}

			return hoursStr + ':' + minutes;
		}

		function testConvertToMilitary() {
			var test1 = convertToMilitary('3:23 AM');
			var test2 = convertToMilitary('2:43 PM');
			var test3 = convertToMilitary('12:00 AM');
			var test4 = convertToMilitary('12:29 PM');

			$('#test1').html(test1);
			$('#test2').html(test2);
			$('#test3').html(test3);
			$('#test4').html(test4);
		}

		function fakeAvailabilityData() {
			$('#availabilityResult').html('Jason Capili is available - jcapili@scu.edu<br>Helen Chan is avialable - hchan@scu.edu');
		}

		function getAvailabilityData() {

		}

	</script>

</head>
<body>
	<div class="container" >
	<!-- Event create stuff -->
<div class="row match-my-cols">
	<div class="col-sm-1" >
	 <nav class="navbar navbar-fixed-side">
		 <div class="navrow">
		 <img src="img/logo.png">
	 	</div>
		 <div class="navrow">
		 <h6> <?php echo"$username" ?> </h6>
	 	</div>
		<div class="navrow">
		<ul><a href="index.php">Calendar</a></ul>
	 </div>
		<div class="navrow" style="background-color: white; padding: 10px">
		<ul>Availability</ul>
	 </div>
	 <div class="navrow">
	 <ul><a href="logout.php">Logout</a></ul>
	</div>
 </div>


<div class = "col-sm-11">
	<div class="page-header">
		<h2>Availability</h2>
	</div>
	<div id='availableForm'>
		<p>Please enter a start time:</p>
		<div class="container">
	    <div class="row">
	        <div class='col-sm-6'>
	            <div class="form-group">
	                <div class='input-group date' id='datetimepicker1'>
	                    <input type='text' class="form-control" id='addStart'/>
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-time"></span>
	                    </span>
	                </div>
	            </div>
	        </div>
	        <script type="text/javascript">
	            $(function () {
	                $('#datetimepicker1').datetimepicker();
	            });
	        </script>
	    </div>
	</div>

		<p>Please enter an end time:</p>

		<div class="container">
	    <div class="row">
	        <div class='col-sm-6'>
	            <div class="form-group">
	                <div class='input-group date' id='datetimepicker2'>
	                    <input type='text' class="form-control" id='addEnd'/>
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-time"></span>
	                    </span>
	                </div>
	            </div>
	        </div>
	        <script type="text/javascript">
	            $(function () {
	                $('#datetimepicker2').datetimepicker();
	            });
	        </script>
	    </div>
	</div>

		<div>
		<button id='availabilityButton' onclick='fakeAvailabilityData()'>Submit</button>
		</div>

	</div>
</div>
</div>
	<br><br>
	<p id="availabilityResult">
	</p>
	<!-- https://www.w3schools.com/bootstrap/bootstrap_modal.asp -->
	<!-- Modal -->
</div>
</bodY>
</html>
