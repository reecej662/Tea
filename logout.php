<?php

session_start();

if(isset($_SESSION['username']))
    session_unset();

$_SESSION['logout'] = true;
header("Location: login.php?ref=logout");

?>
