<?php
	session_start();
	$_SESSION['id']=$_POST['id'];
	$_SESSION['username']=$_POST['username'];
	$_SESSION['email']=$_POST['email'];
	$_SESSION['firstName']=$_POST['email'];
	$_SESSION['lastName']=$_POST['lastName'];
?>
