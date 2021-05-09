<?php
require 'functions.php';
$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$type = isset($_POST['type']) ? $_POST['type'] : "";
    
    $user = validateUser($dynamodb, $marshaler, $username, $type);    
    if (empty($user)) {
    	 header("Location:index.php?err=1");
    }else{
	     $ep = $marshaler->unmarshalValue($user['password']);
	     if($ep == $password) {
	        session_start();
	        $_SESSION['user'] = $username;
	        header("Location:mainpage.html");

	     }else
	      header("Location:index.php?err=1");
	    }


?>