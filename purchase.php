<?php
    require_once("backend/db.php");
    require_once("backend/purchase.php");
    //
    authenticate_user();
    //
    $page = get_param("page",1);
?>
<!DOCTYPE html>
<html>

<?php include("head.php"); ?>

<body class="top-navigation">
    <script>
        function setValue(order_id,field,value){
            $.ajax({
              method: "POST",
              url: "purchase.php",
              data: "cmd=SET&order_id="+order_id+"&field="+field+"&value="+value
            })
            .done(function( msg ) {});
        }
    </script>
    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
        
        <?php include("nav.php"); ?>
        
        <!-- Content BEGIN --> 
          <div class="main-inner">
            <form action="purchase.php" method="post" name="Form2">
            <input type="hidden" name="cmd" id="cmd" value=""/>
            <input type="hidden" name="page" id="page" value=""/>
            <div class="container-fluid">
                <div class="section-operator asin-isbn">
                
                <div class="top-btn custom-bott">
                     <div class="col-md-12">
                     <div class="col-md-9 text-left">
                        <button class="btn btn-sm btn-primary" onclick="document.getElementById('cmd').value='EXECUTE ACTION';document.forms.Form2.submit();">EXCUTE ACTION</button>
                     </div>
                     <div class="col-md-3">
                     <div class="ad-top">
                            <span> <a target="_blank" href="http://kurofune.club"><img src="img/hp-btn.png" alt="" ></a></span>
                             <span> <a target="_blank" href="https://sellercentral.amazon.com/gp/homepage.html"><img src="img/amazon-ad.png" alt="" /></a></span>
                        </div>
                     </div>
                     
                     </div>
                 </div>
                
               
                <div class="col-md-12">
                <div class="pull-right right-btn-div">
                    <button class="btn btn-sm btn-default btn1">Save PURCHASE</button>
                    <button class="btn btn-sm btn-default ">Shift to EARNING</button>
                </div>  
                </div>
                
                <div class="col-md-12">
                   <div class="selling-section large-table-scroll">
                       <div class="table-responsive">
                           <table  class="table table-bordered text-center">
                              <tr>
                                <td  class="middle-text" valign="middle" style="width: 40px;">Delete</td>
                                <td valign="middle" class="middle-text" style="width: 50px;">Order ID</td>
                                <td valign="middle" class="middle-text"  style="width: 50px;">Order Time</td>
                                <td valign="middle" class="middle-text"  style="width: 70px;">ASIN</td>
                                <td colspan="2" valign="middle" class="middle-text"  style="width: 100px;">Images</td>
                                <td valign="middle" class="middle-text"   style="width: 150px;">Commodity</td>
                                <td valign="middle" class="middle-text"   style="width: 50px;">Country</td>
                                <td valign="middle" class="middle-text" style="width: 70px;">Ranking</td>
                                <td valign="middle"class="middle-text"  style="width: 70px;">Selling Price</td>
                              
                                <td valign="middle" class="middle-text" style="width: 70px;">Amazon Commission</td>
                                  <td valign="middle"class="middle-text"  style="width: 70px;">Purchase Price</td>
                                  <td valign="middle" class="middle-text"   style="width:40px;">Purchase</td>
                                <td valign="middle"class="middle-text"  style="width: 70px;">Comparision</td>
                                <td valign="middle"class="middle-text"  style="width: 70px;">Shipping Rate</td>
                                <td valign="middle" class="middle-text"   style="width: 40px;">Shipped</td>
                              </tr>
                              <?php
                                $link = connect();
                                $start = ($page-1) * PAGE_COUNT;
                                $limit = " LIMIT $start,".PAGE_COUNT;
                                $res = query(TABLE_PURCHASE,[]," WHERE user_id=".$_SESSION['user_id']." ORDER BY order_date DESC",$link,$limit);
                                while($row = mysqli_fetch_array($res)){
                              ?>
                              <tr>
                                <td  class="middle-text" > <input type="checkbox" name="delete[]" value="<?php echo $row['order_id'] ?>"> </td>
                                <td valign="middle"  class="middle-text" ><?php echo $row['order_id']; ?></td>
                                <td  class="middle-text">
                                 <?php echo str_replace(" ","<br>",$row['order_date']); ?>
                                </td>
                                <td class="break-span color-blue" style="width: 8%;">
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>"><?php echo $row['asin'];?> (JP)</a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>"><?php echo $row['asin'];?> (US)</a></span>
                                </td>
                               <td class="middle-text" style="width: 50px;"><img style="max-width:50px" src="<?php echo $row['image_us']; ?>" alt=""></td>
                               <td class="middle-text" style="width: 50px;"><img style="max-width:50px" src="<?php echo $row['image_jp']; ?>" alt=""></td>
                                <td class="break-span color-blue" style="width:8%">
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>"><?php echo $row['commodity']!="" && strlen($row['commodity'])>16 ? substr($row['commodity'],0,15)."..." : $row['commodity'];?></a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>"><?php echo $row['commodity']!="" && strlen($row['commodity'])>16 ? substr($row['commodity'],0,15)."..." : $row['commodity'];?></a></span>
                                </td>
                                <td valign="middle" class="middle-text"  > <img src="img/<?php echo strtolower($row['country']); ?>.jpg" /></td>
                                <td style="vertical-align: middle;">#<?php echo $row['ranking']; ?></td>
                                <td class="break-span color-blue">
                                <span class="default-color">¥<?php echo round($row['selling_price'] * US_JP_RATE); ?></span>
                                <span class="default-color">$<?php echo round($row['selling_price'],2); ?></span>
                                </td>
                                <td class="break-span color-blue">
                                <span class="default-color">¥<?php echo round($row['selling_price'] * 0.15 * US_JP_RATE); ?></span>
                                <span class="default-color">$<?php echo round($row['selling_price'] * 0.15,2); ?></span>
                                </td>
                                <td style="vertical-align: middle;" class="middle-text">
                                 <input type="text"  class="form-control text-custom-height" value="<?php echo round($row['purchase_price']); ?>" onchange="setValue('<?php echo $row['order_id']?>','purchase_price',this.value);">
                                </td>
                                <td  class="middle-text" ><input type="checkbox"<?php echo $row['purchased']=="1" ? " checked" : "";?> onchange="setValue('<?php echo $row['order_id']?>','purchased',this.checked ? '1' : '0');"></td>
                                <td class="break-span color-blue" style="width:5%;">
                                  <!-- <a target="_blank" href="compare.php?asin=<?php echo $row['asin'];?>">Compare</a> -->
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>">Compare (JP)</a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>">Compare (US)</a></span>
                                </td>
                                 <td class="middle-text">
                                <input type="text"  class="form-control text-custom-height" value="<?php echo round($row['shipping']); ?>" onchange="setValue('<?php echo $row['order_id']?>','shipping',this.value);">
                                </td>
                                <td  class="middle-text" ><input type="checkbox"<?php echo $row['shipped']=="1" ? " checked" : "";?> onchange="setValue('<?php echo $row['order_id']?>','shipped',this.checked?'1':'0');"></td>
                              </tr>
                              <?php } ?>
                            </table>
                            
                       </div>
                   </div>
                   
                   <?php
                    $res_count = query(TABLE_PURCHASE,[]," WHERE user_id=".$_SESSION['user_id'],$link);
                    include("pagination.php");
                    ?>
                </div>
                
                
                
                    
                </div>
            </div>
            </form>
        </div>
        <!-- Content END --> 
        
        <?php include("footer.php"); ?>

        </div>
    </div>
    <?php include("scripts.php"); ?>

</body>

</html>
