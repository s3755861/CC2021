<?php
require 'functions.php';
$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$type = isset($_POST['type']) ? $_POST['type'] : "";
    
    $user = validateUser($dynamodb, $marshaler, $username);    
    if (empty($user)) {
    	 header("Location:login.php?err=1");
    }else{
	     $ep = $marshaler->unmarshalValue($user['password']);
	     if($ep == $password) {
	        session_start();
	        $_SESSION['user'] = $username;
	        if($type == 'Passenger'){
	        	isNotDriving($dynamodb, $marshaler, $username);
	        	header("Location:passengermain.php");
	        }else
	        isDriving($dynamodb, $marshaler, $username);
	        header("Location:drivermain.php");

	     }else
	      header("Location:login.php?err=1");
	    }


?>