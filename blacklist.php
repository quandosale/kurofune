<?php
    require_once("backend/db.php");
    require_once("backend/blacklist.php");
    //
    authenticate_user();
    //
    $page = get_param("page",1);
    //
    $link = connect();
    $long_where = "";
    $blacklist_keywords = [];
    $blacklist_keywords_res = query(TABLE_BLACKLIST_KEYWORDS,[]," WHERE user_id=".$_SESSION['user_id'],$link);
    while($blacklist_keywords_row = mysqli_fetch_array($blacklist_keywords_res)){
      $blacklist_keywords[] = trim($blacklist_keywords_row['keyword']);
      $long_where.=" OR commodity LIKE '%".trim($blacklist_keywords_row['keyword'])."%'";
    }
    if($long_where!="")
      $long_where = substr($long_where, 4);
    //
    $blacklist_asins = [];
    $blacklist_asins_res = query(TABLE_BLACKLIST,[]," WHERE user_id=".$_SESSION['user_id'],$link);
    while($blacklist_asins_row = mysqli_fetch_array($blacklist_asins_res)){
      $blacklist_asins[$blacklist_asins_row['asin']] = 1;
    }
?>
<!DOCTYPE html>
<html>

<?php include("head.php"); ?>

<body class="top-navigation">

    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
        
        <?php include("nav.php"); ?>
        
        <!-- Content BEGIN --> 
		<div class="main-inner">
            <div class="container-fluid">
                <div class="section-operator asin-isbn">
                <form action="blacklist.php" method="post" enctype="multipart/form-data" name="Form2">
                  <input type="hidden" name="page" id="page" value=""/>
                <div class="top-btn">
                		<input type="hidden" id="cmd" name="cmd" value=""/>
                     <div class="col-md-12">
                     
                   <div class="col-md-2">
                    <div class="country-select">
                    <div class="dropdown">
                    <button class="btn btn-block btn-default dropdown-toggle" type="button" data-toggle="dropdown">USA
                    <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                    <!-- <li><a href="#">USA</a></li> -->
                    </ul>
                    </div>
                    </div>
                    </div>
                     
                     <div class="col-md-5">
                        <label>CSV Import</label>
                        <button class="btn btn-sm btn-primary" onclick="$('#file-upload').trigger('click');return false;">File</button>
                        <input id="file-upload" name="csv_file" type="file" style="display:none"/>
                          <button class="btn btn-sm btn-primary" onclick="$('#cmd').val('IMPORT');document.forms.Form2.submit();">Import</button>
                           <button class="btn btn-sm btn-default" onclick="$('#cmd').val('EXECUTE ACTION');document.forms.Form2.submit();">EXCUTE DELETION</button>
                     </div>
                     
                     <div class="col-md-5">
                     <div class="ad-top">
                            <span> <a target="_blank" href="http://kurofune.club"><img src="img/hp-btn.png" alt="" ></a></span>
                             <span> <a target="_blank" href="https://sellercentral.amazon.com/gp/homepage.html"><img src="img/amazon-ad.png" alt="" /></a></span>
                        </div>
                     </div>
                     
                     </div>
                 </div>
                
                    <div class="col-md-5">
                      <div class="execute-left">
                        <div class="table-responsive" >
                             <table width="100%"  class="table table-bordered text-center">
                                  <tr>
                                    <td>DELETE</td>
                                    <td>ASIN/ISBN</td>
                                    <td>IMAGE</td>
                                    <td>COMMODITY</td>
                                  </tr>
                                  <?php
	                                	$start = ($page-1) * PAGE_COUNT;
	                                	$limit = " LIMIT $start,".PAGE_COUNT;
	                                	//$res = query(TABLE_BLACKLIST,[],"",$link,$limit);
                                    $res = query(TABLE_PRODUCTS,[]," WHERE (status=".STATUS_BL." OR ($long_where)) and user_id=".$_SESSION['user_id']." ORDER BY `updated` DESC",$link,$limit);
	                                	while($row = mysqli_fetch_array($res)){
	                               ?>
                                  <tr>
                                    <td><input type="checkbox" name="delete[]" value="<?php echo $row['asin']; ?>"></td>
                                    <td><?php echo $row['asin']; ?></td>
                                    <td><img style="width:50px;" src="<?php echo $row['image_us']; ?>"/></td>
                                    <td><?php echo $row['commodity']; ?></td>
                                  </tr>
                                  <?php } ?>
                                </table>

                                
                        </div>
                        
                        <?php 
	                    $res_count = query(TABLE_PRODUCTS,[]," WHERE (status=".STATUS_BL." OR ($long_where)) and user_id=".$_SESSION['user_id'],$link,$limit);
	                    include("pagination.php");
	                   ?>
                        
                    </div>
                    </div>
                	</form>
                    
                    <form action="blacklist.php" method="post" name="Form1">
                    <input type="hidden" id="cmd2" name="cmd" value=""/>
                    <div class="col-md-7">
                        
                        
                        
                            <div class="url-form">
                            
                            <div class="form-group">
                            <label for="email" style="padding-left: 0;" class="col-md-3">KEY WORD REGISTRATION</label>
                            <div class="col-md-4">
                            <input class="form-control text-custom-height" name="keyword" type="text">
                            </div>
                            <div class="col-md-5">
                            <button type="submit" class="btn btn-sm btn-primary" onclick="$('#cmd2').val('ADD');document.forms.Form1.submit();">Add</button>
                            <button type="submit" class="btn btn-sm btn-default" onclick="$('#cmd2').val('DELETE');document.forms.Form1.submit();">DELETE KEYWORD</button>
                            </div>
                            </div>
                            
                            </div>
                        
                        
                        <div class="url-table">
                        <h4>REGISTERED KEY WORD</h4>
                           <div class="utable-height">
                            <div class="table-responsive">
                                
                                <table width="100%"  class="table table-bordered">
                                	<?php
                                      $blacklist_keywords_res = query(TABLE_BLACKLIST_KEYWORDS,[]," WHERE user_id=".$_SESSION['user_id'],$link);
                                      while($row = mysqli_fetch_array($blacklist_keywords_res)){
                                	?>
                                  <tr>
                                    <td style="width:50px"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
                                    <td><?php echo $row['keyword']; ?></td>
                                  </tr>
                                  <?php } ?>
                                </table>
                                    
                               </div>
                        </div>
                        </div>
                        
                        
                        
                    </div>
                    
                </div>
            </div>

        </div>
        <!-- Content END --> 
        
        <?php include("footer.php"); ?>

        </div>
    </div>
    <?php include("scripts.php"); ?>

</body>

</html>
