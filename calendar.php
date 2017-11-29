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

	<title>TA Emergency Availability</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

	<!--     Fonts and icons     -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

	<!-- CSS Files -->
    	<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
   	<!--<link href="assets/css/material-kit.css" rel="stylesheet"/>-->

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
		    	editable: true,
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
					document.getElementById('edit').onclick = function() {
						toggleVisibility('editForm');
						editEvent(calEvent, userId);
					};
					document.getElementById('delete').onclick = function() {
						// toggleVisibility('editForm');
						deleteEvent(calEvent, userId);
					};
			},

			eventDrop: function(event, delta, revertFunc, jsView, ui, view ) {
				changeEvent(event,globalUserId);
			}
		    });

		    getEvents(userId, function (data) {
			events = data["events"];

			if(events) {
				$('#calendar').fullCalendar('removeEvents');
				$('#calendar').fullCalendar('renderEvents', events, true);
			}
		    });
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
					end: moment(events[index]["end"]).format(),
					editable: true
				})
			}

			return eventArray;
		}

		function getSchedule(userId) {
			events = [];
			userId = userId;

			getEvents(userId, function(data) {
				events = data;
			});
		}

		function addEvent(userId, eventTitle, startTime, endTime) {
			var event = {
				title: eventTitle,
				start: startTime,
				end: endTime,
				editable: true
			};

			createEvent(event, function(data) {
				refreshCalendar();
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
				end: endTime,
				editable: true
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
			// console.log(startTime);
			// console.log(endTime);

			if(moment(endTime).diff(moment(startTime)) < 0) {
				alert('Invalid event time(s). Please try again.');
			} else {
				toggleVisibility('editForm');
				var event = {
					id: calEvent["id"],
					userId: userId,
					eventId: calEvent["id"],
					title: eventTitle,
					start: startTime,
					end: endTime,	
					editable: true
				}

				updateEvent(event, function(data) {
					refreshCalendar();
				})
			}
		}

		function changeEvent(calEvent, userId) {
			var eventStart = $.fullCalendar.moment(calEvent.start).format('YYYY-MM-DD HH:mm:ss');
			var eventEnd = $.fullCalendar.moment(calEvent.end).format('YYYY-MM-DD HH:mm:ss');

			var event = {
				id: calEvent["id"],
				userId: userId,
				eventId: calEvent["id"],
				title: calEvent.title,
				start: eventStart,
				end: eventEnd,	
				editable: true
			}

			updateEvent(event, function(data) {
				refreshCalendar();
			})
		}

		function deleteEvent(calEvent, userId) {
			var id = calEvent["id"];

			deleteEventBackend(id, function(data) {
				refreshCalendar();
			})
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

	</script>
</head>
<body>
	<nav class="navbar navbar-transparent navbar-absolute" style="background-color: #b30739;">
		<div class="container">
			<div class="navbar-header">
	    			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example">
	        			<span class="sr-only">Toggle navigation</span>
		            		<span class="icon-bar"></span>
		            		<span class="icon-bar"></span>
		            		<span class="icon-bar"></span>
	    			</button>
	    			<a class="navbar-brand" style="color:white;" href="">My Schedule</a>
	    		</div>

	    		<div class="collapse navbar-collapse" id="navigation-example">
	    			<ul class="nav navbar-nav navbar-right">
					<li>
						<a style="color:white;" href="http://students.engr.scu.edu/~rjackson/Tea/index.php">
							Home
						</a>
					</li>
					<li>
						<a style="color:white;" href="http://students.engr.scu.edu/~rjackson/Tea/available.php">
							Find Available
						</a>
					</li>
					<li>
						<a style="color:white;" href="">
							Calendar
						</a>
					</li>
					<li>
						<a style="color:white;" href="http://students.engr.scu.edu/~rjackson/Tea/logout.php">
							Logout <?php echo $_SESSION['username'];?>
						</a>
	    				</li>
				</ul>
	    		</div>
		</div>
	</nav>

	<div class="container">
	<div id='calendar' style="width: 90%; text-align: center;"></div>

	<!-- Event create stuff -->

	<br><br><br>
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
		<button id='editEventButton'>Submit</button>
		</div>

	</div>

	<!-- https://www.w3schools.com/bootstrap/bootstrap_modal.asp -->
	<!-- View/Edit Modal -->
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
	</div>
	<br><br><br><br><br>
</bodY>
</html>
