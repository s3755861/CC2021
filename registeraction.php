<?php
require 'functions.php';
$password = isset($_POST['password']) ? $_POST['password'] : "";
$username = isset($_POST['username']) ? $_POST['username'] : "";
$type = isset($_POST['type']) ? $_POST['type'] : "";

    
    $result = validateUser($dynamodb, $marshaler, $username, $type);
    if (empty($result)) {
    createUser($dynamodb, $marshaler, $username, $password, $type);
    header("Location:index.php");
    }else{
        header("Location:register.php?err=1");
    }



     
?>