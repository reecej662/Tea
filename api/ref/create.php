<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate event object
include_once '../objects/event.php';
 
$database = new Database();
$db = $database->getConnection();
 
$product = new Event($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set event property values
$product->name = $data->name;
$product->price = $data->price;
$product->description = $data->description;
$product->category_id = $data->category_id;
$product->created = date('Y-m-d H:i:s');
 
// create the event
if($product->create()){
    echo '{';
        echo '"message": "Event was created."';
    echo '}';
}
 
// if unable to create the event, tell the user
else{
    echo '{';
        echo '"message": "Unable to create event."';
    echo '}';
}
?>