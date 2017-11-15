<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");


// include database and object files
include_once '../config/config.php';
include_once '../objects/event.php';
 
// instantiate database and event object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$event = new Event($db);

$event->userId = isset($_GET['userId']) ? $_GET['userId'] : null;
	
// query products
$stmt = $event->read();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){
 
    // products array
    $events_arr=array();
    $events_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
        $event_item=array(
            "id" => $id,
            "userId" => $userId,
            "eventId" => $eventId,
            "title" => $title,
            "start" => $start,
            "end" => $end
        );
 
        array_push($events_arr["events"], $event_item);
    }
 
    echo json_encode($events_arr);
}
 
else{
    echo json_encode(
        array("message" => "No events found.")
    );
}
?>
