<?php
require 'functions.php';
$email = isset($_POST['email']) ? $_POST['email'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
    
    $user = validateUser($dynamodb, $marshaler, $email);    
    if (empty($user)) {
    	 header("Location:index.php?err=1");
    }else{
	     $ep = $marshaler->unmarshalValue($user['password']);
	     if($ep == $password) {
	        session_start();
	        $_SESSION['user'] = $marshaler->unmarshalValue($user['user_name']);
	        $_SESSION['email'] = $email;
	        header("Location:main.php");

	     }else
	      header("Location:index.php?err=1");
	    }


?>