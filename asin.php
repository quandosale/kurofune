<?php
    require_once("backend/db.php");
    require_once("backend/asin.php");
    //
    authenticate_user();
    //
    $page = get_param("page",1);
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
                    <div class="col-md-6">
                      <form action="asin.php" method="post" name="Form2">
                        <input type="hidden" name="page" id="page" value=""/>
                        <input type="hidden" id="asin_url_id" name="asin_url_id" value=""/>
                        <input type="hidden" id="cmd" name="cmd" value="DELETE"/>
                      <div class="execute-left">
                       <div class="right-btn">
                        <a href="javascript:{}" onclick="if(confirm('Really delete the selected items?')){document.getElementById('cmd').value='DELETE';document.forms.Form2.submit();}" class="btn btn-default btn-sm">EXCUTE DELETION</a>
                       </div>
                        <div class="table-responsive">
                             <table width="100%"  class="table table-bordered text-center">
                                  <tr>
                                    <td>DATE</td>
                                    <td>URL</td>
                                    <td colspan="2">CSV</td>
                                    <td>DELETE</td>
                                  </tr>
                                  <?php
                                      $link = connect();
                                      $start = ($page-1) * PAGE_COUNT;
                                      $limit = " LIMIT $start,".PAGE_COUNT;
                                      /* $res = query(TABLE_ASIN_URLS,[]," WHERE status=1 and user_id=".$_SESSION['user_id'],$link,$limit); */
									  $q="select * from asin_urls WHERE status=1 and user_id=".$_SESSION['user_id'].$limit;
									  $res=mysqli_query($link,$q);
                                      while($row = mysqli_fetch_array($res)){
                                  ?>
                                  <tr>
                                      <td><?php echo $row['updated']?></td>
                                      <td><a target="_blank" href="<?php echo $row['url']; ?>"><?php echo substr($row['url'],0,40)."..."?></a></td>
                                      <td><button class="btn btn-xs btn-primary">OK</button></td>
                                      <td><button class="btn btn-xs btn-default" onclick="document.getElementById('cmd').value='DL';document.getElementById('asin_url_id').value='<?php echo $row['id']?>';document.forms.Form2.submit()">DL</button></td>
                                      <td><input type="checkbox" name="ids[]" value="<?php echo $row['id'] ?>"/></td>
                                  </tr>
                                  <?php } ?>
                            </table>
                                
                        </div>
                        
                        <?php 
                            /* $res_count = query(TABLE_ASIN_URLS,array()," WHERE status=1 and user_id=".$_SESSION['user_id'],$link); */
							
							 $q2="select * from asin_urls WHERE status=1 and user_id=".$_SESSION['user_id'];
							 $res=mysqli_query($link,$q2);
                            include("pagination.php");
                        ?>
                        
                    </div>
                    </form>
                    </div>
                    
                    
                    <div class="col-md-6">
                        <form action="asin.php" method="post" name="Form1">
                            <input type="hidden" id="cmd2" name="cmd" value=""/>
                        <div class="ad-top">
                            <span> <a target="_blank" href="http://kurofune.club"><img src="img/hp-btn.png" alt="" ></a></span>
                             <span> <a target="_blank" href="https://sellercentral.amazon.com/gp/homepage.html"><img src="img/amazon-ad.png" alt="" /></a></span>
                        </div>
                            <div class="url-form">
                            <div class="form-group">
                            <label for="email" class="col-md-2" style="padding-left: 0;">URL</label>
                            <div class="col-md-8">
                            <input type="text" class="form-control" id="email" name="url">
                            </div>
                            <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary" onclick="document.getElementById('cmd2').value='ADD';document.forms.Form1.submit();">Add</button>
                            </div>
                            </div>
                            </div>
                        
                        <div class="url-btn">
                           <button class="btn btn-primary btn-sm" onclick="document.getElementById('cmd2').value='START';document.forms.Form1.submit();">START</button>
                           <button class="btn btn-primary btn-sm" onclick="document.getElementById('cmd2').value='SUSPEND';document.forms.Form1.submit();">SUSPEND</button>
                            <button class="btn btn-default btn-sm" onclick="if(confirm('Really delete the selected items?')){document.getElementById('cmd2').value='DELETE';document.forms.Form1.submit();}">DELETE</button>
                        </div>
                        
                        
                        <div class="url-table">
                        <h4>URL in queue</h4>
                           <div class="utable-height">
                                <div class="table-responsive">
                                    <table width="100%"  class="table table-bordered">
                                           <?php
                                                $link = connect();
                                                $res = query(TABLE_ASIN_URLS,array()," WHERE (status=0 or status=2) and user_id=".$_SESSION['user_id'],$link);
                                                while($row = mysqli_fetch_array($res)){
                                            ?>
                                            <tr>
                                                <td style="width:50px"><input type="checkbox" name="ids[]" value="<?php echo $row['id'] ?>"/></td>
                                                <td><a target="_blank" href="<?php echo $row['url']; ?>"><?php echo substr($row['url'],0,65)."..."?></a></td>
                                                <td style="width:50px;color:red"><?php echo $row['status']==2 ? 'Working...' : ''?></td>
                                            </tr>
                                            <?php } ?>
                                    </table>    
                                </div>
                            </div>
                        </div>
                        </form>
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
