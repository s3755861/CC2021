<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css"/>
</head>
<body>
    <div id="login">
        <h1>Login</h1>
        <form action="loginaction.php" method="post">
            <input class="um" type="text" required="required" placeholder="username" name="username"></input>
            <input class="um" type="password" required="required" placeholder="password" name="password"></input>
            <center>
            <input type="radio"  name="type" value="Passenger">Passenger
            <input type="radio"  name="type" value="Driver">Driver
            </center><br>
            <p align="center" style="color:red;font-size:15px;"><?php
            $err = isset($_GET["err"]) ? $_GET["err"] : "";
            switch ($err) {
                case 1:
                    echo "username or password is incorrect！";
                    break;

            } ?> </p>
            <button class="but" type="submit">Login</button>
        </form>
        <br>
        <p align="center"> Not have an account yet? <a href="register.php">Sign Up!</p>
    </div>
</body>
</html>