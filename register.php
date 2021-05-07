<!DOCTYPE html>
<html>
<head><title>Register</title>
<meta name="content-type"; charset="UTF-8"></head>
<body> 
<div class="content" align="center"> 
<div class="header"> <h2>Register Page</h2> </div>  
<div class="middle"> 
<form action="registeraction.php" method="post" enctype="multipart/form-data"> 
<table border="0">
<tr><td>Email:</td> 
<td><input type="text" id="email" name="email" required="required"></td></tr> 
<tr><td>Username:</td> <td><input type="username" id="username" name="username" required="required"></td></tr>
<tr><td>Password:</td> <td><input type="password" id="password" name="password" required="required"></td></tr>
<tr><td colspan="2" align="center" style="color:red;font-size:15px;"> 
<?php
$err = isset($_GET["err"]) ? $_GET["err"] : "";
switch ($err) {
    case 1:
        echo "The email already exists！";
        break;
    }
?>

</td></tr> 
<tr> <td colspan="2" align="center"> 
<input type="submit" id="register" name="register" value="Register"></td></tr>
</table>
</form>
</div>
</div>
</body>
</html>

