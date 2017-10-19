const express = require('express');
const app = express();
const bodyParser = require('body-parser');
const path = require('path');
const moment = require('moment')

var schedules = makeSampleSchedule();

app.use(express.static('dist'));
app.use(bodyParser.json());

app.get('/', function (req, res) {
  res.sendFile(path.join(__dirname + '/index.html'));
});

app.get('/schedule', function (req, res) {
	var userId = req.query.userId;

	schedules = makeSampleSchedule();

	res.send(schedules[userId]);
});

app.get('/available', function (req, res) {
	var userId = req.query.userId;
	var startTime = req.query.startTime;
	var endTime = req.query.endTime;

	res.send("available");
});

app.post('/create', function (req, res) {
	console.log(req.body);
	
	var newEvent = makeSampleEvent(req.body['title'], req.body['startTime'], req.body['endTime']);

	res.send(newEvent);
});

app.listen(3000, function () {
  console.log('TEA running on port 3000');
});

function makeSampleSchedule() {
	return {
		"test1":[
			makeSampleEvent("Test Event", '2017-10-18T19:00:00', '2017-10-18T22:00:00'),
			makeSampleEvent("Test Event", '2017-10-19T19:00:00', '2017-10-19T22:00:00'),
			makeSampleEvent("Test Event", '2017-10-20T19:00:00', '2017-10-20T22:00:00'),
			makeSampleEvent("Test Event", '2017-10-21T19:00:00', '2017-10-21T22:00:00'),
		],
		"test2":[
			makeSampleEvent("Test Event", '2017-10-21T19:00:00', '2017-10-21T22:00:00'),
			makeSampleEvent("Test Event", '2017-10-22T19:00:00', '2017-10-22T22:00:00'),
			makeSampleEvent("Test Event", '2017-10-23T19:00:00', '2017-10-23T22:00:00'),
		],
		"test3":[
			makeSampleEvent("Test Event", '2017-10-23T19:00:00', '2017-10-23T22:00:00'),
			makeSampleEvent("Test Event", '2017-10-24T19:00:00', '2017-10-24T22:00:00'),
		],
		"rjackson@scu.edu":[
			makeSampleEvent('Coen 174', '2017-10-23T16:15:00', '2017-10-23T17:20:00'),
			makeSampleEvent('Coen 174', '2017-10-25T16:15:00', '2017-10-25T17:20:00'),
			makeSampleEvent('Coen 174L', '2017-10-25T21:15:00', '2017-10-25T24:00:00'),
			makeSampleEvent('Coen 174', '2017-10-27T16:15:00', '2017-10-27T17:20:00'),
			makeSampleEvent('Csci 168', '2017-10-23T20:00:00', '2017-10-23T21:05:00'),
			makeSampleEvent('Csci 168', '2017-10-25T20:00:00', '2017-10-25T21:05:00'),
			makeSampleEvent('Csci 168', '2017-10-27T20:00:00', '2017-10-27T21:05:00'),
			makeSampleEvent('Mgmt 198E', '2017-10-24T02:30:00', '2017-10-24T04:05:00'),
			makeSampleEvent('Arts 197A', '2017-10-24T09:00:00', '2017-10-24T10:00:00'),
			makeSampleEvent('Coen 194', '2017-10-27T20:30:00', '2017-10-24T21:35:00')
		]
	}
}

function makeSampleEvent(title, startDate, endDate) {
	var timezone = getTimezone();

	var start = moment(startDate + timezone);
	var end = moment(endDate + timezone);

	var myEvent = {
		id: 1,
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