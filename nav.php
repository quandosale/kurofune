<div class="col-md-12">
   <div class="logo-wrapper clearfix">
     <div class="left-logo">
         <a href="#"><img src="img/logo.png"  alt=""/> Administration</a>
     </div>
     <div class="rate">
        <span class="rate-icon">
          <?php
            echo "Welcome ".$_SESSION['user']." (<a style='color:red' href='?cmd=logout'>Logout</a>)"
          ?>
          <img src="img/rate-icon.png"  alt=""/> Rate
        </span>
        <span class="rate-drop">
         <div class="dropdown">
          <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">USD
          <span class="caret"></span></button>
          <ul class="dropdown-menu">
          </ul>
        </div>
        </span>
        <span class="rate-icon"> <?php echo US_JP_RATE; ?></span>
     </div> 
  </div>
</div>
<div class="row border-bottom white-bg">
  <div class="menu-main">
    <nav class="navbar navbar-static-top" role="navigation">
      <div class="navbar-header">
          <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
              <i class="fa fa-reorder"></i>
          </button>
      </div>
      <div class="nav-small navbar-collapse collapse" id="navbar">
          <ul class="nav navbar-nav">
              <li<?php echo strpos($_SERVER['REQUEST_URI'],"asin.php")!==false ?  ' class="active"' : '' ?>>
                  <a aria-expanded="false" role="button" href="asin.php"> ASIN/ISBM</a>
              </li>
              
              <li<?php echo strpos($_SERVER['REQUEST_URI'],"extraction.php")!==false ?  ' class="active"' : '' ?>>
                  <a aria-expanded="false" role="button" href="extraction.php">Data Extraction/Sell</a>
              </li>
              
              
              <li<?php echo strpos($_SERVER['REQUEST_URI'],"selling.php")!==false ?  ' class="active"' : '' ?>>
                  <a aria-expanded="false" role="button" href="selling.php">Selling</a>
              </li>
              
              <li<?php echo strpos($_SERVER['REQUEST_URI'],"purchase.php")!==false ?  ' class="active"' : '' ?>>
                  <a aria-expanded="false" role="button" href="purchase.php">Purchase</a>
              </li>
              
              <li<?php echo strpos($_SERVER['REQUEST_URI'],"earnings.php")!==false ?  ' class="active"' : '' ?>>
                  <a aria-expanded="false" role="button" href="earnings.php">Earnings</a>
              </li>
              
              
              <li<?php echo strpos($_SERVER['REQUEST_URI'],"blacklist.php")!==false ?  ' class="active"' : '' ?>>
                  <a aria-expanded="false" role="button" href="blacklist.php">Black List</a>
              </li>
              
              <li<?php echo strpos($_SERVER['REQUEST_URI'],"settings.php")!==false ?  ' class="active"' : '' ?>>
                  <a aria-expanded="false" role="button" href="settings.php">Settings</a>
              </li>

              <!-- <li>
                  <a aria-expanded="false" role="button" href="<?php echo ADMIN_URL; ?>">USER-SECTION</a>
              </li> -->

          </ul>

      </div>
    </nav>
  </div>   
</div>