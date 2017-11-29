<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once 'config/config.php';

// instantiate database and event object
$database = new Database();
$conn = $database->getConnection();

// Get availability start and end parameters
$start = isset($_GET['start']) ? $_GET['start'] : null;
$end = isset($_GET['end']) ? $_GET['end'] : null;

$query = "SELECT DISTINCT e.userId FROM events e WHERE e.end >= '" . $start . "' AND e.start <= '" . $end . "'";
$stmt = $conn->prepare($query);
$stmt->execute();

$num = $stmt->rowCount();

// check if more than 1 busy user found
if($num>0){

    // busy users array
    $busy_users_arr=array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        array_push($busy_users_arr, $row['userId']);
    }

    $userQuery = "SELECT * FROM users WHERE id NOT IN ( '" . implode($busy_users_arr, "', '") . "' )";

} else {
    // no busy users, so all users
    $userQuery = "SELECT * FROM users";
}

$userStmt = $conn->prepare($userQuery);
$userStmt->execute();

if($userStmt->rowCount() > 0) {
    $available_users_arr = array();

    while($row = $userStmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        array_push($available_users_arr, $row);
    }   

    echo json_encode($available_users_arr);

} else {
    echo json_encode(
        array("message" =>  "No users found")
    );
}
?>
