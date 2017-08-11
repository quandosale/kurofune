<?php
    require_once("backend/db.php");
    require_once("backend/earnings.php");
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
                
                <div class="top-btn">
             <div class="col-md-12">
            
                
                <div class="row">
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
                  <div class="calendar-main">
                     <form class="form-inline" method="post" name="Form2">
                      <input type="hidden" name="page" id="page" value=""/>
                    
                     
                      <div class="form-group">
                        <input class="form-control" id="cal_from" name="cal_from"  placeholder="2014-02-20" value="<?php echo $cal_from?>" type="text">
                      </div>
                      <div class="form-group">
                        <input class="form-control" id="cal_to" name="cal_to" placeholder="2016-11-20" value="<?php echo $cal_to?>" type="text">
                      </div>
                      
                      <button type="submit" class="btn btn-sm btn-primary">AGGREGATE</button>
                    </form>
                  </div>
                  </div>
                  
                  
                  <div class="col-md-5">
                        <div class="ad-top">
                            <span> <a target="_blank" href="http://kurofune.club"><img src="img/hp-btn.png" alt="" ></a></span>
                             <span> <a target="_blank" href="https://sellercentral.amazon.com/gp/homepage.html"><img src="img/amazon-ad.png" alt="" /></a></span>
                        </div>
                        
                        
                    </div>
                  
                  </div>
                  
             </div>
         </div>

             <div class="info-table" style="margin-top:10px;">
             <div class="col-md-12">
                 <div class="table-responsive">
                      <table width="100%"  class="table table-bordered">
                      <tr>
                        <td width="9%" align="center" valign="middle">Sales Amount</td>
                        <td width="15%" align="center" valign="middle">Amazon<br>Commission</td>
                        <td width="11%" align="center" valign="middle">Purchase Amount<br> Total</td>
                        <td width="12%" align="center" valign="middle">Shipping Rate<br> TOtal</td>
                        <td width="10%" align="center" valign="middle">Number <br> The Sold</td>
                        <td width="10%" align="center" valign="middle">Net Profit <br>Amount </td>
                        <td width="11%" align="center" valign="middle">Net Profit<br> Rate</td>
                        <td width="12%" align="center" valign="middle">Sale Unit Price<br> in Average</td>
                        <td width="10%" align="center" valign="middle">Daily Number of Sold <br>In Average</td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle">¥<?php echo ceil($aggregate_row['sell_sum'] * US_JP_RATE) ?></td>
                        <td align="center" valign="middle">¥<?php echo ceil($aggregate_row['comm_sum'] * US_JP_RATE) ?></td>
                        <td rowspan="2" align="center" valign="middle" style="vertical-align:middle">¥<?php echo ceil($aggregate_row['purchase_sum']) ?></td>
                        <td rowspan="2" align="center" valign="middle" style="vertical-align:middle">¥<?php echo ceil($aggregate_row['shipping_sum']) ?></td>
                        <td rowspan="2" align="center" valign="middle" style="vertical-align:middle"><?php echo $aggregate_row['c'] ?></td>
                        <td rowspan="2" align="center" valign="middle" style="vertical-align:middle">¥<?php $profit = ($aggregate_row['sell_sum']* US_JP_RATE-$aggregate_row['comm_sum']* US_JP_RATE-$aggregate_row['purchase_sum']-$aggregate_row['shipping_sum']); echo ceil($profit); ?></td>
                        <td rowspan="2" align="center" valign="middle" style="vertical-align:middle"><?php echo round($profit/($aggregate_row['sell_sum']* US_JP_RATE)*100,1)?>%</td>
                        <td rowspan="2" align="center" valign="middle" style="vertical-align:middle">¥<?php echo ceil(($aggregate_row['sell_sum']* US_JP_RATE)/$aggregate_row['c'])?></td>
                        <td rowspan="2" align="center" valign="middle" style="vertical-align:middle"><?php echo round(($aggregate_row['c']/days_diff($cal_from,$cal_to)),2)?></td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle">$<?php echo $aggregate_row['sell_sum'] ?></td>
                        <td align="center" valign="middle">$<?php echo $aggregate_row['comm_sum'] ?></td>
                        </tr>
                    </table>

                 </div>
                  </div>    
             </div>   
                
                
                
                
                
                
                <div class="col-md-12" style="margin-top:10px;">
                   <div class="selling-section large-table-scroll-new">
                       <div class="table-responsive">
                           <table  class="table table-bordered text-center" style="width:100%">
                              <tr>
                                <td valign="middle" class="middle-text">Sale Order ID </td>
                                <td valign="middle" class="middle-text">Order Time</td>
                                <td valign="middle" class="middle-text">ASIN</td>
                                <td valign="middle" class="middle-text">Commodity</td>
                                <td valign="middle" class="middle-text">Ranking</td>
                                <td valign="middle" class="middle-text">Sales Amount</td>
                                <td valign="middle" class="middle-text">Amazon Charge</td>
                                <td valign="middle" class="middle-text">Purchase <br>Amount</td>
                                <td valign="middle" class="middle-text">Shipping Rate</td>
                                <td valign="middle"class="middle-text">Net Profit </td>
                              </tr>
                              <?php
                                $link = connect();
                                $start = ($page-1) * PAGE_COUNT;
                                $limit = " LIMIT $start,".PAGE_COUNT;
                                $res = query(TABLE_PURCHASE,[]," WHERE user_id=".$_SESSION['user_id']." ORDER BY order_date DESC",$link,$limit);
                                while($row = mysqli_fetch_array($res)){
                              ?>
                              <tr>
                                <td valign="middle"  class="middle-text" ><?php echo $row['order_id']?></td>
                                 <td valign="middle"  class="middle-text" ><?php echo str_replace(" ","<br>",$row['order_date']); ?></td>
                                  <td class="break-span color-blue">
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>"><?php echo $row['asin'];?> (JP)</a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>"><?php echo $row['asin'];?> (US)</a></span>
                                </td>
                                <td class="break-span color-blue">
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>"><?php echo $row['commodity']!="" && strlen($row['commodity'])>50 ? substr($row['commodity'],0,49)."..." : $row['commodity'];?></a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>"><?php echo $row['commodity']!="" && strlen($row['commodity'])>50 ? substr($row['commodity'],0,49)."..." : $row['commodity'];?></a></span>
                                </td>
                                <td style="vertical-align: middle;">#<?php echo $row['ranking']; ?></td>
                                <td class="break-span color-blue"  style="padding:0px;">
                                  <span class="default-color">¥<?php echo round($row['selling_price'] * US_JP_RATE); ?></span>
                                  <span class="default-color">$<?php echo round($row['selling_price'],2); ?></span>
                                </td>
                                <td class="break-span color-blue"  style="padding:0px;">
                                  <span class="default-color">¥<?php echo round($row['selling_price'] * 0.15 * US_JP_RATE); ?></span>
                                  <span class="default-color">$<?php echo round($row['selling_price'] * 0.15,2); ?></span>
                                </td>
                                <td valign="middle" class="middle-text"  >¥<?php echo round($row['purchase_price']); ?></td>
                                <td style="vertical-align: middle;">¥<?php echo round($row['shipping']); ?></td>
                                <td  style="vertical-align: middle;">
                                   <?php
                                    $net_profit = ($row['selling_price'] * US_JP_RATE) - $row['purchase_price'] - $row['shipping'];
                                    echo "¥".round($net_profit);
                                   ?>
                                </td>
                             
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

        </div>
        <!-- Content END --> 
        
        <?php include("footer.php"); ?>

        </div>
    </div>
    <?php include("scripts.php"); ?>

</body>

</html>
