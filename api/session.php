<?php
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

	$data = json_decode(file_get_contents("php://input"));

        session_start();
        $_SESSION['id']=$data->id;
        $_SESSION['username']=$data->username;
        $_SESSION['email']=$data->email;
        $_SESSION['firstName']=$data->firstName;
        $_SESSION['lastName']=$data->lastName;

        echo 'Session created';
?>

