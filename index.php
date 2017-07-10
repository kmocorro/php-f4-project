<?php

session_start();

if(isset($_SESSION['user_session'])!="")
{
	header("Location: home.php");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Log in to Fab4 Lot Extractor</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> 
<script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
<script type="text/javascript" src="validation.min.js"></script>
<link href="style.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript" src="script.js"></script>

<style>
html { 
  background: url() no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}



body:hover {
    opacity: 1.0;
    filter: alpha(opacity=100); /* For IE8 and earlier */
}

</style>
</head>

<body>
    
<div class="signin-form">

	<div class="container">
        
       <form class="form-signin" method="post" id="login-form">
        <br><br>
        <h2 class="form-signin-heading" style="text-align:center;">Fab4</h2>
        
        <div id="error">
        <!-- error will be shown here -->
        </div>
        
        <div class="form-group">
        <input type="email" class="form-control" placeholder="Email" name="user_email" id="user_email" />
        <span id="check-e"></span>
        </div>
        
        <div class="form-group">
        <input type="password" class="form-control" placeholder="Password" name="password" id="password" />
        </div>
        
        <div class="loginSpaced">
            <div class="form-group">
                <button type="submit" class="btn btn-default" name="btn-login" id="btn-login" >
                Log In
                </button> 
            </div>  
        </div>

        <div class="viewOpt"  class ="button secondary">
            <a href="/optselect.php" >Tool Outs Monitoring System</a>
        </div>

        <hr />

        


      </form>

    </div>

</div>



    
<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>