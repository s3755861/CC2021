<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Sign In</title>
        <link rel="stylesheet" href="login.css" />
        <script type="text/javascript" src="jquery-3.6.0.min.js"></script>
    </head>
    <body>
  <div class="mainbody middle">
    <form class="form-box front">
      <div>
        <h2><center>Login</center></h2>
      </div>
      <div>
        <input class="input-normal" type="text" id="username" name="username" placeholder="Username" required="required"/>
        <input class="input-normal" type="password" id="password" name="password" placeholder="Password" required="required"/>
        <center>
        <input type="radio"  name="type" value="Passenger"><label style="color: white; font-size: 15px">Passenger</label>
        <input type="radio"  name="type" value="Driver"><label style="color: white; font-size: 15px">Driver</label>
        </center>
            <button class="btn-submit" type="submit" style="margin-top: 30px">
                  <a href="loginaction.php">Login</a>  
            </button>
      </div>
      <div>
        <p style="margin-top: 40px">Have not account yet? please</p>
        <p>Click here to <a id="register" style="text-decoration:underline; font-size:18px">sign up</a></p>   
        <script type="text/javascript">

              $("#register").click(function() {
                  $(".middle").toggleClass("middle-flip");
              });    
            </script>
      </div>
    </form>

    <form class="form-box back">
      <div>
        <center>
        <h2>Sign up</h2>
        </center>
      </div>
      <div>
        <input class="input-normal" type="text" id="username" name="username" placeholder="Username" required="required">
        <input class="input-normal" type="password" id="password" name="password" placeholder="Username" required="required">
        <center>
        <input type="radio"  name="type" value="Passenger"><label style="color: white; font-size: 15px">Passenger</label>
        <input type="radio"  name="type" value="Driver"><label style="color: white; font-size: 15px">Driver</label>
        </center>
        <button class="btn-submit" type="submit" style="margin-top: 30px">
             <a href="registeraction.php">Sign up</a>
        </button>
      </div>
      <div>
        <p style="margin-top: 40px">Have an account? you can</p>
        <p>Click here to <a id="login" style="text-decoration:underline; font-size:18px">sign in</a></p>     
        <script type="text/javascript">
              $("#login").click(function() {
                  $(".middle").toggleClass("middle-flip");
              });
        </script>   
      </div>
    </form>
  </div>
</body>
</html>