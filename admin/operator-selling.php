<?php
  require_once("../backend/db.php");
  require_once("../backend/userSales.php");
  //
  authenticate();
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
#country_id {
	-moz-appearance : none;
	-webkit-appearance : none;
	position: relative;
}
.icn {
	position: absolute;
	top: 3px;
	right: 7px;
	cursor: pointer;
}
.icn i {
	font-size: 12px;
}
</style>
</head>
<body class="top-navigation">
<div id="wrapper">
  <div id="page-wrapper" class="gray-bg">
    <div class="col-md-12">
      <div class="logo-wrapper clearfix">
        <div class="left-logo"> <a href="<?php echo TOOL_URL; ?>"><img src="../img/logo.png"  alt=""/> Administration</a> </div>
      </div>
    </div>
    <div class="row border-bottom white-bg">
      <div class="menu-main">
        <nav class="navbar navbar-static-top" role="navigation">
          <div class="navbar-header">
            <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> <i class="fa fa-reorder"></i> </button>
            <!--<a href="#" class="navbar-brand">Inspinia</a>--> 
          </div>
          <div class="navbar-collapse collapse" id="navbar">
            <ul class="nav navbar-nav">
              <li> <a aria-expanded="false" role="button" href="operator-users.php"> User</a> </li>
              <li> <a aria-expanded="false" role="button" href="operator-sales.php">Sales</a> </li>
              <li  class="active"> <a aria-expanded="false" role="button" href="operator-selling.php">Selling</a> </li>
              
              <!--<li class="dropdown">
                        <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href="#">Menu item</a></li>
                            <li><a href="#">Menu item</a></li>
                            <li><a href="#">Menu item</a></li>
                            <li><a href="#">Menu item</a></li>
                        </ul>
                    </li>-->
              
            </ul>
            <ul class="nav navbar-top-links navbar-right">
              <li> <a href="login.html"> <i class="fa fa-sign-out"></i> Log out </a> </li>
            </ul>
          </div>
        </nav>
      </div>
      <div class="country-drop">
        <div class="col-md-12">
          <div class="row">
            <!--<div class="col-md-2">
              <div class="country-select">
                <div class="dropdown">
                  <button class="btn btn-block btn-default dropdown-toggle" type="button" data-toggle="dropdown">Country <span class="caret"></span></button>
                  <ul class="dropdown-menu" id="country_id" name="country_id" >
                    <li><a href="#">India</a></li>
                    <li><a href="#">USA</a></li>
                    <li><a href="#">Japan</a></li>
                  </ul>
                </div>
              </div>
            </div>-->
            <div class="col-md-2">
              <div class="country-select">
                <div class="dropdown">
                  <select id="country_id" name="country_id" class="btn btn-block btn-default dropdown-toggle" style="padding: 0px 12px;">
                    <option value="1" style="background: #fff;padding-top: 10px;">USA</option>
                    <option value="2" style="background: #fff;padding-top: 10px;">CANADA</option>
                    <option value="3" style="background: #fff;padding-top: 10px;">JAPAN</option>
                  </select>
                  <span class="icn"><i class="fa fa-caret-down" aria-hidden="true"></i></span> </div>
              </div>
            </div>
          </div>
        </div>
      </div>
	  
         <div class="top-btn">
             <div class="col-md-12">
            
                
                <div class="row">
                   
                  
                  <div class="col-md-6">
                  <div class="calendar-main">
                     <form class="form-inline">
                    
                     
                      <div class="form-group">
                         <input type="text"  class="form-control datepicker" id="start_date" placeholder="calendar">
                      </div>
                      
                       <div class="form-group center-width">
                       <strong>~</strong>
                      </div>
                            
                      <div class="form-group">
                        <input type="text" class="form-control datepicker" id="end_date"  placeholder="calendar">
                      </div>
                      
                      <button type="button" class="btn btn-sm btn-primary" id="aggregrate">AGGREGATE</button>
                    </form>
                  </div>
                  </div>
                  
                <div class="clear"></div>
                
                <div class="col-md-12">
                  <div class="csv-top">
                    <button class="btn-download btn-sm btn-default" id="export">CSV File Download</button>
                  </div>
                </div>
                  
                  </div>
                  
             </div>
         </div>
    
    </div>
    <div class="main-inner">
      <div class="container-fluid">
        <div class="section-operator">
          <div class="table-main">
            <div class="table-responsive" id="response_change">
              <table width="100%" class="table table-bordered text-center">
                <tr>
                  <td style="text-align:center;">RELEASE DATE</td>
                  <td style="text-align:center;">ASIN/ISBN</td>
                  <td style="text-align:center;">SELLING PRICE</td>
                </tr>
                <?php   $link = connect();
							$sql="SELECT release_date,price_us,asin FROM  products";
							if($res = mysqli_query($link, $sql)){
							$rowCount = mysqli_num_rows($res);
							$row = mysqli_fetch_assoc($res);
					?>
                <?php if($rowCount>0){
							while($row = mysqli_fetch_assoc($res)){
						?>
                <tr>
                  <td><?php if(date('Y',strtotime($row['release_date']))!=1970) {echo date('Y-m-d',strtotime($row['release_date']));}else{ echo "N/A";}?></td>
                  <td><?php echo $row['asin'];?></td>
                  <td>$
                    <?php  echo '  '.round($row['price_us']);?></td>
                </tr>
                <?php }}}else{?>
                <tr>
                  <td colspan="3">No Record Found !</td>
                </tr>
                <?php }?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer">
      <div class="text-center"> <strong>Copyright</strong> &copy 2016 | Kurofune . All Rights Reserved </div>
    </div>
  </div>
</div>

<!-- Mainly scripts --> 
<script src="js/jquery-2.1.1.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<script type="text/javascript">
        $(document).ready(function(){
			$("#export").click(function(){
				$("table").tableToCSV();
			});
			 $('#country_id').change(function(){
				var selectedValue = this.value;
				var start_date = $('#start_date').val();
				var end_date = $('#end_date').val();

				//make the ajax call
				$.ajax({
					url: 'ajax/operator-selling.php',
					type: 'POST',
					data: {country_id : selectedValue,start_date : start_date,end_date : end_date},
					success: function(response) {
						$('#response_change').html(response);
					}
				});
			});
			$('#aggregrate').click(function(){
				var selectedValue = $('#country_id').val();
				var start_date = $('#start_date').val();
				var end_date = $('#end_date').val();
				//make the ajax call
				$.ajax({
					url: 'ajax/operator-selling.php',
					type: 'POST',
					data: {country_id : selectedValue,start_date : start_date,end_date : end_date},
					success: function(response) {
						$('#response_change').html(response);
					}
				});
			});
            $( ".datepicker" ).datepicker();
        });
        </script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="js/jquery-2.1.1.js"></script> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
<script src="js/jquery.tabletoCSVuserSales.js"></script>
</body>
</html>
