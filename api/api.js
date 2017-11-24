// Event API functions
function createEvent(event, completion) {
	$.ajax({
		url: "http://104.131.9.190/api/event/create.php",
		type: 'POST',
		data: JSON.stringify(event),
		dataType: 'json',
		contentType: 'application/json',
		success: function(data) {
			completion(data);
		}
	});
}

function getEvents(userId, completion) {
	$.ajax({
		url: "http://104.131.9.190/api/event/read.php?userId=" + userId,
		type: "GET",
		data: {},
		dataType: 'json',
		contentType: 'application/json',
		success: function(data) {
			completion(data);
		}
	});
}

function updateEvent(event, completion) {
	$.ajax({
		url: "http://104.131.9.190/api/event/update.php",
		type: "POST",
		data: JSON.stringify(event),
		dataType: 'json',
		contentType: 'application/json',
		success: function(data) {
			completion(data);
		}
	});		
}

function deleteEventBackend(id, completion) {
	$.ajax({
		url: "http://104.131.9.190/api/event/delete.php",
		type: "POST",
		data: JSON.stringify({
			id: id
		}),
		dataType: 'json',
		contentType: 'application/json',
		success: function(data) {
			completion(data);
		}
	});
}

function makeEvent(title, startDate, endDate) {
	var myEvent = {
		eventId: 5,
		userId: 1,
		title: title,
		start: moment(startDate).format("YYYY-MM-DD HH:mm:ss"),
		end: moment(endDate).format("YYYY-MM-DD HH:mm:ss"),
		editable: true
	};

	return myEvent;
}

// User API functions
function createUser(user, completion) {
	$.ajax({
		url: "http://104.131.9.190/api/user/create.php",
		type: "POST",
		data: JSON.stringify(user),
		dataType: 'json',
		contentType: 'application/json',
		success: function(data) {
			completion(data);
		}
	});
}

function getUser(username, completion) {
	$.ajax({
		url: "http://104.131.9.190/api/user/read_one.php?username=" + username,
		type: "GET",
		data: {},
		dataType: 'json',
		contentType: 'application/json',
		success: function(data) {
			completion(data);
		}
	})
}

function updateUser(user, completion) {
	$.ajax({
		url: "http://104.131.9.190/api/user/update.php",
		type: "POST",
		data: JSON.stringify(user),
		dataType: 'json',
		contentType: 'application/json',
		success: function(data) {
			completion(data);
		}
	})
}

function deleteUser(id, completion) {
	$.ajax({
		url: "http://104.131.9.190/api/user/delete.php",
		type: "DELETE",
		data: JSON.stringify({
			id: id
		}),
		dataType: 'json',
		contentType: 'application/json',
		success: function(data) {
			completion(data);
		}
	})
}

// Functions for testing
function makeSampleEvent(title, userId, startDate, endDate) {
	var timezone = "-07:00";

	var start = moment(startDate + timezone).format("YYYY-MM-DD HH:mm:ss");
	var end = moment(endDate + timezone).format("YYYY-MM-DD HH:mm:ss");

	var myEvent = {
		eventId: new Date().getUTCMilliseconds(),
		userId: userId,
		title: title,
		start: start,
		end: end,
		editable: true
	};

	return myEvent;
}

function makeSampleEvents() {
	return [
		makeSampleEvent('Coen 174 1', 1, '2017-11-23T16:15:00', '2017-11-23T17:20:00'),
		makeSampleEvent('Coen 174 2', 1, '2017-11-25T16:15:00', '2017-11-25T17:20:00'),
		makeSampleEvent('Coen 174L', 1, '2017-11-25T21:15:00', '2017-11-25T24:00:00'),
		makeSampleEvent('Coen 174 3', 1, '2017-11-27T16:15:00', '2017-11-27T17:20:00'),
		makeSampleEvent('Csci 168 1', 1, '2017-11-23T20:00:00', '2017-11-23T21:05:00'),
		makeSampleEvent('Csci 168 2', 1, '2017-11-25T20:00:00', '2017-11-25T21:05:00'),
		makeSampleEvent('Csci 168 3', 1, '2017-11-27T20:00:00', '2017-11-27T21:05:00'),
		makeSampleEvent('Mgmt 198E', 1, '2017-11-24T02:30:00', '2017-11-24T04:05:00'),
		makeSampleEvent('Arts 197A', 1, '2017-11-24T09:00:00', '2017-11-24T10:00:00'),
		makeSampleEvent('Coen 194', 1, '2017-11-27T20:30:00', '2017-11-24T21:35:00')
	]
}