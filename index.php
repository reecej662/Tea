<?php
	session_start();
	if(isset($_SESSION['username'])) {
  		echo $_SESSION['username'];
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
		    toggleVisibility('addForm');
			toggleVisibility('editForm');

		    $('#calendar').fullCalendar({
		    	dayClick: function(date, jsEvent, view) {
					toggleVisibility('addForm');
					document.getElementById('addEventButton').onclick = function() {
						toggleVisibility('addForm');
						addEvent(userId, date.format());
					};
		    	},

		    	eventClick: function(calEvent, jsEvent, view) {
		    		var temp = "Title: " + calEvent.title +
		    					"<br>Start Time: " + calEvent.start.format('LLLL') +
		    					"<br>End Time: " + calEvent.end.format('LLLL');
		    		$('#message').html(temp);
		    		$('#myModal').modal().show();
					document.getElementById('editEventButton').onclick = function() {
						toggleVisibility('editForm');
						editEvent(calEvent, userId);
					};
					document.getElementById('delete').onclick = function() {
						// toggleVisibility('editForm');
						deleteEvent(calEvent, userId);
					};
				},
				// eventDrop: function(event, delta, revertFunc, jsView, ui, view ) {
				// 	updateEvent(event,userId,delta);
				// }
		    })

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

		function toggleVisibility(form) {
		    var x = document.getElementById(form);
			
			if (x.style.display === "none") {
       			x.style.display = "block";
    		} else {
        		x.style.display = "none";
   			}		
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

	</script>

	<style>
		p {
			font-family: Arial;
		}

		body { 
			padding: 50px;
		}
	</style>
</head>
<body>
	<a href="available.php">Availability</a>
	<a href="logout.php">Logout</a>
	<br><br>
	<div id='calendar' style='width:50%; height:50%;'></div>

	<!-- Event create stuff -->

	<br><br><br><br><br><br><br><br><br><br><br><br>
	<div id='addForm'>
		<p>Please enter your event title:<br></p>
		<input type="text" name="addTitle" id="addTitle"><br>

		<p>Please enter a start time:<br></p>
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
	                $('#datetimepicker1').datetimepicker({
	                    format: 'LT'
	                });
	            });
	        </script>
	    </div>
	</div>

		<p>Please enter an end time:<br></p>

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
	                $('#datetimepicker2').datetimepicker({
	                    format: 'LT'
	                });
	            });
	        </script>
	    </div>
	</div>

		<div>
		<button id='cancel' onclick="toggleVisibility('addForm')">Cancel</button>
		<button id='addEventButton'>Submit</button>
		</div>

	</div>

	<div id='editForm'>
		<p>Please enter your new event title:<br></p>
		<input type="text" name="editTitle" id="editTitle"><br>

		<p>Please enter a new start time:<br></p>
		<div class="container">
	    <div class="row">
	        <div class='col-sm-6'>
	            <div class="form-group">
	                <div class='input-group date' id='datetimepicker3'>
	                    <input type='text' class="form-control" id='editStart'/>
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-time"></span>
	                    </span>
	                </div>
	            </div>
	        </div>
	        <script type="text/javascript">
	            $(function () {
	                $('#datetimepicker3').datetimepicker({
	                    format: 'LT'
	                });
	            });
	        </script>
	    </div>
	</div>

		<p>Please enter a new end time:<br></p>

		<div class="container">
	    <div class="row">
	        <div class='col-sm-6'>
	            <div class="form-group">
	                <div class='input-group date' id='datetimepicker4'>
	                    <input type='text' class="form-control" id='editEnd'/>
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-time"></span>
	                    </span>
	                </div>
	            </div>
	        </div>
	        <script type="text/javascript">
	            $(function () {
	                $('#datetimepicker4').datetimepicker({
	                    format: 'LT'
	                });
	            });
	        </script>
	    </div>
	</div>

		<div>
		<button id='cancel' onclick="toggleVisibility('editForm')">Cancel</button>
		<!-- <button id='delete'>Delete Event</button> -->
		<button id='editEventButton'">Submit</button>
		</div>

	</div>

	<!-- https://www.w3schools.com/bootstrap/bootstrap_modal.asp -->
	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Event Information</h4>
	      </div>
	      <div class="modal-body">
	      	<p id='message'></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal" id='delete'>Delete Event</button>
		<button type="button" class="btn btn-default" data-dismiss="modal" id='edit' onclick="toggleVisibility('editForm')">Edit Event</button>
	      </div>
	    </div>

	  </div>
	</div>
</bodY>
</html>
