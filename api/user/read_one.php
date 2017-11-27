<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/config.php';
include_once '../objects/user.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare event object
$user = new User($db);
 
// set ID property of event to be edited
//$user->id = isset($_GET['id']) ? $_GET['id'] : die();
$user->username = isset($_GET['username']) ? $_GET['username'] : die();
 
// read the details of event to be edited
$user->readOne();
 
// create array
$user_arr = array(
    "id" =>  $user->id,
    "username" => $user->username,
    "email" => $user->email,
    "firstName" => $user->firstName,
    "lastName" => $user->lastName,
    "created" => $user->created
);
 
// make it json format
print_r(json_encode($user_arr));
?>
