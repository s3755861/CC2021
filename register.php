<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="login.css"/>
</head>
<body>
    <div id="login">
        <h1>Register</h1>
        <form action="registeraction.php" method="post">
            <input class="um" type="text" required="required" placeholder="username" name="username"></input>
            <input class="um" type="password" required="required" placeholder="password" name="password"></input><br>
            <p align="center" style="color:red;font-size:15px;"><?php
            $err = isset($_GET["err"]) ? $_GET["err"] : "";
            switch ($err) {
                case 1:
                    echo "username already exist!";
                    break;

            } ?> </p>
            <button class="but" type="submit">Register</button>
        </form>
        <br>
        <p align="center">Have an account yet? <a href="login.php">Sign In!</p>
    </div>
</body>
</html>

