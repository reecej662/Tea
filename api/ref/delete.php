<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
 
// include database and object file
include_once '../config/database.php';
include_once '../objects/event.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare event object
$product = new Event($db);
 
// get event id
$data = json_decode(file_get_contents("php://input"));
 
// set event id to be deleted
$product->id = $data->id;
 
// delete the event
if($product->delete()){
    echo '{';
        echo '"message": "Event was deleted."';
    echo '}';
}
 
// if unable to delete the event
else{
    echo '{';
        echo '"message": "Unable to delete object."';
    echo '}';
}
?>