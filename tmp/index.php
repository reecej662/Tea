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
					//	$('#editTitle').attr('value',calEvent.title);
					//	$('#editStart').val(calEvent.start.format('h:ss A'));
					//	$('#editEnd').val(calEvent.end.format('h:ss A'));
					//};
					//document.getElementById('editEventButton').onclick = function() {
					//	editEvent(calEvent, globalUserId);
					//};
					//document.getElementById('delete').onclick = function() {
					//	deleteEvent(calEvent, globalUserId);
					//};
				//},

				eventDrop: function(event, delta, revertFunc, jsView, ui, view ) {
					changeEvent(event,globalUserId);
				}
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
					end: moment(events[index]["end"]).format(),
					editable: true
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
			}
			else {
				toggleVisibility('editForm');
				var event = {
					id: calEvent["id"],
					userId: 1,
					eventId: 105,
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
			// console.log(eventStart);
			// console.log(eventEnd);

			var event = {
				id: calEvent["id"],
				userId: 1,
				eventId: 105,
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

		// function timeOK(start, end) {
		// 	var startHour = moment(start).hour();
		// 	var endHour = moment(end).hour();
		// 	var startMin = moment(start).minute();
		// 	var endMin = moment(end).minute();
		// 	var temp = true;

		// 	if(endHour > startHour ) {
		// 		temp = false;
		// 	} else if(endHour == startHour) {
		// 		if( endMin > startMin ) {
		// 			temp = false;
		// 		}
		// 	}

		// 	return temp;
		// }

		// function resetServer() {
		// 	$.ajax({
		// 		url: window.location.href + "tea/reset",
		// 		type: "POST",
		// 		data: JSON.stringify({
		// 			userId: globalUserId
		// 		}),
		// 		dataType: 'json',
		// 		contentType: 'application/json',
		// 		success: function(data) {
		// 			events = data;
		// 			refreshCalendar();
		// 		}
		// 	});
		// }

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
		
		function testDatabase() {
			var testObject = {
				field1: "value1",
				field2: "value2",
				object: {
					val1: "is this working",
				}
			}
			var lab = {
				name: "174 Lab",
				ta: "dick",
				questions: [
					{
						title: "How to make design report",
						student: "hasdf",
						likes:7
					},
					{
						title: "How to make database",
						student: "reece",
						likes:1000
					}
				]
			}

			//databaseRef.

			$.ajax({
				url: window.location.href + "tea/testDatabase",
				type: "POST",
				data: JSON.stringify(lab),
				dataType: 'json',
				contentType: 'application/json',
				success: function(data) {
					alert(data);
				}
			})
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

</bodY>
</html>
