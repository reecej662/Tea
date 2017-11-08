<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/config.php';
include_once '../objects/user.php';

// instantiate database and event object
$database = new Database();
$db = $database->getConnection();

// initialize object
$user = new User($db);

// query products
$stmt = $user->read();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // products array
    $users_arr=array();
    $users_arr["records"]=array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $user_item=array(
            "id" => $id,
            "username" => $username,
            "email" => $email,
            "firstName" => $firstName,
            "lastName" => $lastName,
            "created" => $created
        );

        array_push($users_arr["records"], $user_item);
   }

    echo json_encode($users_arr);
}

else{
    echo json_encode(
        array("message" => "No users found.")
    );
}
?>