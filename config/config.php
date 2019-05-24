<?php
ob_start();//turn on output buffering
session_start();

$time_zone=date_default_timezone_set('America/Los_Angeles');

$con = mysqli_connect("localhost", "root", "", "social"); //Connection variable

if(mysqli_connect_errno())
{
	echo "Failed to connect: " . mysqli_connect_errno();
}


?>
