const express = require('express');
const app = express();
const bodyParser = require('body-parser');
const path = require('path');
const moment = require('moment')
const firebase = require("firebase");
const database = firebase.database;

var schedules = makeSampleSchedule();
initializeDatabase();

app.use(express.static('dist'));
app.use(bodyParser.json());

app.get('/', function (req, res) {
	res.sendFile(path.join(__dirname + '/index.html'));
});

app.get('/tea/allTas', function (req, res) {
	res.send(schedules);
})

app.get('/tea/:userId/schedule', function (req, res) {
	var userId = req.params.userId;

	res.send(schedules[userId]);
});

app.post('/tea/testDatabase', function (req, res) {
	var body = req.body;
	var path = "testRound2";

	saveToDatabase(path, body, function() {
		console.log('Success');
	})

	res.send('Worked');
})

app.post('/tea/available', function (req, res) {
	var userId = req.body["userId"];
	var availableUsers = [];

	var start = moment(req.body["start"]);
	var end = moment(req.body["end"]);

	var availabilityStart, availabilityEnd = null;

	for (var key in schedules) {
		var available = true;
		availabilityStart = null;
		availabilityEnd = null;

		schedules[key].forEach(function (event) {	
			if(event.start.diff(end) < 0 && event.end.diff(start) > 0) {
				available = false;
			} else {
				if((availabilityStart == null || event.end.diff(start) > availabilityStart.diff(start)) && event.end.diff(start) <= 0) {
					availabilityStart = event.end;
				}

				if((availabilityEnd == null || event.start.diff(end) < availabilityEnd.diff(end)) && event.start.diff(end) >= 0) {
					availabilityEnd = event.start;
				}
			}
		});

		if(availabilityStart == null) {
			availabilityStart = start;
		}

		if(availabilityEnd == null) {
			availabilityEnd = end;
		}

		if(available) {
			availableUsers.push({
				userId: key,
				availabilityStart: availabilityStart,
				availabilityEnd: availabilityEnd
			});
		}
	}

	res.send(availableUsers);
});

app.post('/tea/:userId/addEvent', function (req, res) {	
	var newEvent = makeSampleEvent(req.body['title'], req.body['start'], req.body['end']);

	schedules[req.params.userId].push(newEvent);
	res.send(newEvent);
});

app.delete('/tea/:userId/deleteEvent/:eventId', function(req, res) {
	var events = schedules[req.params.userId];
	var eventId = req.params.eventId

	events.forEach(function (item) {
		if (item["id"] == eventId) {
			var index = events.indexOf(item);
			events.splice(index, 1);
		}
	});

	res.send(events);

});

app.post('/tea/reset', function(req, res) {
	schedules = makeSampleSchedule();

	res.send(schedules[req.body['userId']]);
});

app.listen(8080, function () {
  console.log('TEA running on port 8080');
});

function makeSampleSchedule() {
	return {
		"test1":[
			makeSampleEvent("Test Event 1", '2017-10-18T19:00:00', '2017-10-18T22:00:00'),
			makeSampleEvent("Test Event 2", '2017-10-19T19:00:00', '2017-10-19T22:00:00'),
			makeSampleEvent("Test Event 3", '2017-10-20T19:00:00', '2017-10-20T22:00:00'),
			makeSampleEvent("Test Event 4", '2017-10-21T19:00:00', '2017-10-21T22:00:00'),
		],
		"test2":[
			makeSampleEvent("Test Event 1", '2017-10-21T19:00:00', '2017-10-21T22:00:00'),
			makeSampleEvent("Test Event 2", '2017-10-22T19:00:00', '2017-10-22T22:00:00'),
			makeSampleEvent("Test Event 3", '2017-10-23T19:00:00', '2017-10-23T22:00:00'),
		],
		"test3":[
			makeSampleEvent("Test Event 1", '2017-10-23T19:00:00', '2017-10-23T22:00:00'),
			makeSampleEvent("Test Event 2", '2017-10-24T19:00:00', '2017-10-24T22:00:00'),
		],
		"rjackson@scu.edu":[
			makeSampleEvent('Coen 174 1', '2017-10-23T16:15:00', '2017-10-23T17:20:00'),
			makeSampleEvent('Coen 174 2', '2017-10-25T16:15:00', '2017-10-25T17:20:00'),
			makeSampleEvent('Coen 174L', '2017-10-25T21:15:00', '2017-10-25T24:00:00'),
			makeSampleEvent('Coen 174 3', '2017-10-27T16:15:00', '2017-10-27T17:20:00'),
			makeSampleEvent('Csci 168 1', '2017-10-23T20:00:00', '2017-10-23T21:05:00'),
			makeSampleEvent('Csci 168 2', '2017-10-25T20:00:00', '2017-10-25T21:05:00'),
			makeSampleEvent('Csci 168 3', '2017-10-27T20:00:00', '2017-10-27T21:05:00'),
			makeSampleEvent('Mgmt 198E', '2017-10-24T02:30:00', '2017-10-24T04:05:00'),
			makeSampleEvent('Arts 197A', '2017-10-24T09:00:00', '2017-10-24T10:00:00'),
			makeSampleEvent('Coen 194', '2017-10-27T20:30:00', '2017-10-24T21:35:00')
		]
	}
}

function initializeDatabase() {
  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyDzGLRiNvfd6K61uVmAvh1j3Q40PiOl4vA",
    authDomain: "taemergencyavailability.firebaseapp.com",
    databaseURL: "https://taemergencyavailability.firebaseio.com",
    projectId: "taemergencyavailability",
    storageBucket: "",
    messagingSenderId: "619527313666"
  };
  firebase.initializeApp(config);
}

function saveToDatabase(path, object, callback) {
	var ref = firebase.database().ref(path);

	ref.set(object);

	callback();
} 

function eventInTime(event, startTime, endTime) {
	var timezone = getTimezone();

	var start = moment(startTime  + timezone);
	var end = moment(endTime + timezone);

	return (event.start.diff(end) < 0 && event.end.diff(start) > 0);
}

function makeSampleEvent(title, startDate, endDate) {
	var timezone = getTimezone();

	var start = moment(startDate + timezone);
	var end = moment(endDate + timezone);

	var myEvent = {
		id: '_' + Math.random().toString(36).substr(2, 9), // Make this legit
		title: title,
		allDay: false,
		start: start,
		end: end,
		url: "",
		rendering: "",
	};

	return myEvent;
}

function getTimezone() {
	var timezone = new Date().getTimezoneOffset() / 60;
	var timezoneInt = parseInt(timezone, 10);

	if (timezoneInt > 0 && timezoneInt < 10)
		timezone = "+0" + timezone + ":00";
	else if (timezoneInt > 0)
		timezone = "+" + timezone + ":00";
	else if (timezoneInt < 0 && timezoneInt > -10)
		timezone = "-0" + Math.abs(timezone) + ":00";

	return timezone;
}