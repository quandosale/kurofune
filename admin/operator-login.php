<?php
    require_once("../backend/db.php");
    require_once("../backend/operator.php");
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kurofune</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
    <link rel="icon" href="../favicon.png" type="image/png" >
    <link href="../css/animate.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/responsive.css" rel="stylesheet">


<style>

body{
	background:#FFF !important;
}
  
</style>

</head>

<body class="gray-bg">

    <div class="col-md-4 center-block">
        <div  class="login-inner">
            <div class="logo-block">

                <h1 class="logo-name"><img src="../img/logo.png"  alt=""/></h1>
                <p class="lead-text">ADMIN Login</p>

            </div>
          
          <div class="login-bg">
            <form class="m-t" role="form" action="" method="post">
                <input type="hidden" name="cmd" value="login"/>
                <font color="red"><?php echo $error; ?></font>
                <div class="form-group">
                     <label>ID</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                 <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                 
                 <!-- <div class="form-group text-left">
                     <label> <input type="checkbox" > Remember me </label>
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

    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
