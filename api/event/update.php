<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/config.php';
include_once '../objects/event.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare user object
$event = new Event($db);
 
// get id of user to be edited
$data = json_decode(file_get_contents("php://input"));
 
// set ID property of user to be edited
$event->id = $data->id;
 
// set user property values
$event->userId = $data->userId;
$event->eventId = $data->eventId;
$event->title = $data->title;
$event->start = $data->start;
$event->end = $data->end;
 
// update the event
if($event->update()){
    echo '{';
        echo '"message": "Event was updated."';
    echo '}';
}
 
// if unable to update the event, tell the user
else{
    echo '{';
        echo '"message": "Unable to update event."';
    echo '}';
}
?>
