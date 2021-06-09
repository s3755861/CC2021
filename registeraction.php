<?php
require 'functions.php';
$password = isset($_POST['password']) ? $_POST['password'] : "";
$username = isset($_POST['username']) ? $_POST['username'] : "";


    
    $result = validateUser($dynamodb, $marshaler, $username);
    if (empty($result)) {
    createUser($dynamodb, $marshaler, $username, $password);
    header("Location:login.php");
    }else{
        header("Location:register.php?err=1");
    }



     
?>