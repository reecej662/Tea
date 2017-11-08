<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/event.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare event object
$event = new Event($db);
 
// set ID property of event to be edited
$event->id = isset($_GET['id']) ? $_GET['id'] : die();
 
// read the details of event to be edited
$event->get();
 
// create array
$event_arr = array(
	"id" => $event->$id,
	"userId" => $event->$userId,
	"eventId" => $event->$eventId,
	"title" => $event->$title,
	"start" => $event->$start,
	"end" => $event->$end 
);
 
// make it json format
print_r(json_encode($event_arr));
?>