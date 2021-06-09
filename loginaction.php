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
	        if($type == "Passenger"){
	        	$arr = pendingTrip($dynamodb, $marshaler, $username);
	        	$aarr = in_progressDr($dynamodb, $marshaler, $username);
	        	if(empty($arr)){
	        		if(empty($aarr)){
	        			isNotDriving($dynamodb, $marshaler, $username);
	        			header("Location:passengermain.php");
	        		}else{
	        		    header("Location:drivermain.php");
	        		}
	        	}else{
	        		header("Location:drivermain.php");
	        	}
	        	
	        }else{
	        	$arr = in_progressPa($dynamodb, $marshaler, $username);
	        	$aarr = pendingPa($dynamodb, $marshaler, $username);
	        	if(empty($arr)){
	        		if(empty($aarr)){
	        			isNotDriving($dynamodb, $marshaler, $username);
	        			header("Location:drivermain.php");
	        		}else{
	        		    header("Location:passengermain.php");
	        		}
	        	}else{
	        		header("Location:passengermain.php");
	        	}
	        }


	     }else
	      header("Location:login.php?err=1");
	    }


?>