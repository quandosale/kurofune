<?php
    require_once("backend/db.php");
    require_once("backend/login.php");
?>
<!DOCTYPE html>
<html>

<?php include("head.php"); ?>

<body class="top-navigation" style="background:#FFF !important;">
    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
        
        <div class="col-md-4 center-block">
          <div  class="login-inner">
            <div class="logo-block">

                <h1 class="logo-name"><img src="img/logo.png"  alt=""/></h1>
                <p class="lead-text">Membership Login</p>

            </div>
          
          <div class="login-bg">
            <form class="m-t" role="form" action="" method="post">
                <input type="hidden" name="cmd" value="login"/>
                <font color="red"><?php echo $error; ?></font>
                <div class="form-group">
                     <label>ID</label>
                    <input type="text" class="form-control" name="username" value=""/>
                </div>
                <div class="form-group">
                 <label>Password</label>
                    <input type="password" class="form-control" name="password" value=""/>
                </div>
                
                 <!-- <div class="form-group text-center">
                 <a href="#"><u>Forget password?</u></a>
                 </div> -->
                 
                 <!-- <div class="form-group text-left">
                     <label> <input type="checkbox" > Remember me </label>
                     <a style="float:right" href="signup.php">Signup</a>
                 </div> -->
                 
              <div class="form-group text-center">
                            <input type="submit" value="Login"  class="customlogin-btn">
              </div>
               
               <!-- <p class="text-muted text-center"><small>Do not have an account?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="register.html">Create an account</a>-->
            </form>
            
            </div>
            <!--<p class="m-t"> <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small> </p>-->
        </div>
        </div>
        </div>
    </div>
    <?php include("scripts.php"); ?>

</body>

</html>
