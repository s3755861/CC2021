<!DOCTYPE html>
<html><head>
<title>Login</title>
<meta name="content-type"; charset="UTF-8">
</head><body> 
<div class="content" align="center"> 
<div class="header"> <h2>Login Page</h2> </div> 
<div class="middle">
<form action="loginaction.php" method="post"> 
<table border="0">
<tr> <td>UserName：</td> <td> <input type="text" id="username" name="username" required="required"> </td></tr> 
<tr> <td>Password：</td> <td><input type="password" id="password" name="password" required="required"></td></tr> 
<tr><td colspan="2" align="center" style="color:red;font-size:15px;"> <?php
$err = isset($_GET["err"]) ? $_GET["err"] : "";
switch ($err) {
    case 1:
        echo "username or password is incorrect！";
        break;

} ?> </td></tr>
<tr><td colspan="2" align="center">
<input type="submit" id="login" name="login" value="Login"></td></tr>
<tr> <td colspan="2" align="center"> No account yet? <a href="register.php">Sign Up!</a></td></tr> 
</table>
</form> 
</div> 

</body>
</html>     