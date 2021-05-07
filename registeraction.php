<?php
require 'functions.php';
$email = isset($_POST['email']) ? $_POST['email'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$username = isset($_POST['username']) ? $_POST['username'] : "";



    $result = validateUser($dynamodb, $marshaler, $email);
    if (empty($result)) {
    createUser($dynamodb, $marshaler, $email, $username, $password);
    header("Location:index.php");
    }else{
        header("Location:register.php?err=1");
    }



     
?>