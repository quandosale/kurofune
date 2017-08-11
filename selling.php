<?php
    require_once("backend/db.php");
    require_once("backend/selling.php");
    //
    authenticate_user();
    //
    $page = get_param("page",1);
    //
    $link = connect();
    /*$res = query(TABLE_OPTIONS,[]," WHERE meta_name='hourly_update'",$link);
    $row = mysqli_fetch_array($res);
    $hourly_update = $row["meta_value"];*/
    //
    $blacklist_keywords = [];
    $blacklist_keywords_res = query(TABLE_BLACKLIST_KEYWORDS,[]," WHERE user_id=".$_SESSION['user_id'],$link);
    while($blacklist_keywords_row = mysqli_fetch_array($blacklist_keywords_res)){
      $blacklist_keywords[] = trim($blacklist_keywords_row['keyword']);
    }
    //
    $blacklist_asins = [];
    $blacklist_asins_res = query(TABLE_BLACKLIST,[]," WHERE user_id=".$_SESSION['user_id'],$link);
    while($blacklist_asins_row = mysqli_fetch_array($blacklist_asins_res)){
      $blacklist_asins[$blacklist_asins_row['asin']] = 1;
    }
    // Sort
    $sortby = get_param("sortby","selling_date");
    $sorttype = get_param("sorttype","DESC");
    $sortindex = get_param("sortindex",0);
?>
<!DOCTYPE html>
<html>

<?php include("head.php"); ?>

<body class="top-navigation">
    <input type="hidden" id="prime_or_not" value="<?php echo $filter_amazon_prime=='1' ? 'price_jp_prime' : 'price_jp'?>"/>
    <script>
      var price_field = document.getElementById("prime_or_not").value;
      var sort_fields = ["ranking","expected_profit","price_us",price_field,"selling_price"];
      var sort_types = ["DESC","ASC"];
      function sort(sortby,th){
        var sort_field = sort_fields[sortby];
        var sort_field_index = parseInt($("#sortindex").val());
        var sort_type = sort_types[sort_field_index];
        $("#sortindex").val((sort_field_index+1)%sort_types.length);
        //
        $("#sortby").val(sort_field);
        $("#sorttype").val(sort_type);
        document.forms.Form2.submit();
      }
    </script>
    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
        
        <?php include("nav.php"); ?>
        
        <!-- Content BEGIN --> 
        <div class="main-inner">
            <form action="selling.php" method="post" name="Form2">
              <input type="hidden" name="cmd" id="cmd" value=""/>
              <input type="hidden" name="page" id="page" value=""/>
              <input type="hidden" name="sortindex" id="sortindex" value="<?php echo $sortindex; ?>"/>
              <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>"/>
              <input type="hidden" name="sorttype" id="sorttype" value="<?php echo $sorttype; ?>"/>
            <div class="container-fluid">
                <div class="section-operator asin-isbn">
                <div class="top-btn">
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
                     
                     <div class="col-md-7">
                        <div class="row">
                     
                        <div class="col-md-8">
                          <label class="m-r-10">Amazon Prime  <input type="checkbox" name="filter_amazon_prime" value="1"<?php echo $filter_amazon_prime=='1' ? ' checked':'';?>> </label>
                          <label>Check Commodity</label>
                        <button class="btn btn-sm btn-primary" onclick="document.getElementById('cmd').value='EXECUTE ACTION';document.forms.Form2.submit();">EXCUTE ACTION</button>
                        </div>
                        
                         <div class="col-md-3">
                        <div class="form-group">
                            <!-- <label><input type="checkbox" name="hourly_update" onchange="document.getElementById('cmd').value='HOURLY UPDATE';document.forms.Form2.submit();" value="1"<?php echo $hourly_update=="1" ? " checked" : "" ?>> Hourly Update</label> -->
                        </div>
                        </div>
                       
                     </div>
                     </div>
                     
                     <div class="col-md-3">
                     <div class="ad-top">
                            <span> <a target="_blank" href="http://kurofune.club"><img src="img/hp-btn.png" alt="" ></a></span>
                             <span> <a target="_blank" href="https://sellercentral.amazon.com/gp/homepage.html"><img src="img/amazon-ad.png" alt="" /></a></span>
                        </div>
                     </div>
                     
                     </div>
                 </div>
                
                
                
                <div class="col-md-12" style="font-size:12px !important">
                   <div class="selling-section large-table-scroll2">
                       <div class="table-responsive">
                          <table  class="table table-bordered table-bordered2 text-center dataTable" style="width:98.8%;margin-bottom:0px;">
                              <tr>
                                <td valign="middle" class="middle-text"   style="width: 6%; vertical-align:middle;font-size:12px">Start selling</td>
                                <td colspan="2"  class="middle-text" valign="middle" style="width: 11.5%;">Image</td>
                                <td valign="middle" class="middle-text" style="width: 7%;font-size:12px">ASIN/ISBN</td>
                                <td valign="middle" class="middle-text"  style="width: 7%;font-size:12px">Commodity</td>
                                <td valign="middle" class="middle-text sorting"  style="width: 5%;font-size:12px;background: url('img/sort_<?php echo $sortby!=""&&$sortby=='ranking'? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(0);">Ranking</a></td>
                                <td valign="middle" class="middle-text sorting"  style="width: 5%;font-size:12px;background: url('img/sort_<?php echo $sortby!=""&&$sortby=='expected_profit'? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(1);">Expected<br>Profit</a></td>
                                <td valign="middle" class="middle-text sorting"   style="width: 5%;font-size:12px;background: url('img/sort_<?php echo $sortby!=""&&$sortby=='price_us'? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(2);">US Price<br>(Sell)</a></td>
                                <td valign="middle" class="middle-text sorting"   style="width: 5%;font-size:12px;background: url('img/sort_<?php echo $sortby!=""&&$sortby=='selling_price'? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(4);">Selling Price</a></td>
                                <td valign="middle" class="middle-text sorting" style="width: 5%;font-size:10px !important;background: url('img/sort_<?php echo $sortby!=""&&($sortby=='price_jp' || $sortby=='price_jp_prime')? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(3);">JP Price<br>(Purchase)</a></td>
                                <td valign="middle"class="middle-text break-span"  style="width: 6%;font-size:12px"><SPAN>Weight</SPAN><SPAN>Dim.</SPAN></td>
                                <td valign="middle"class="middle-text"  style="width: 5%;font-size:12px">Shipping Rate</td>
                                <td valign="middle" class="middle-text" style="width: 5%;font-size:12px">Amazon<br>Comm.</td>
                                <td valign="middle"class="middle-text break-span"  style="width: 8%;font-size:12px"><SPAN>Release date</SPAN><SPAN style="font-size:9px">Sold Numbers (30 Days)</SPAN></td>
                                <td valign="middle" class="middle-text break-span"   style="width:6%;font-size:12px"><SPAN>Sellers Number</SPAN><SPAN>Expected Arrival</SPAN></td>
                                <td valign="middle" class="middle-text"   style="width: 5%;font-size:12px">Compare</td>
                                <td valign="middle" class="middle-text" style="width: 5%;font-size:12px">Delete<br><input type="checkbox" onclick="$(':checkbox[class=\'delete\']').trigger('click')"/></td>
                                <td valign="middle" class="middle-text" style="width: 5%;font-size:12px">BL<br><input type="checkbox" onclick="$(':checkbox[class=\'bl\']').trigger('click')"/></td>
                              </tr>
                              </table>
                        </div>
                    </div>
                   <div class="selling-section large-table-scroll">
                       <div class="table-responsive">
                           <table  class="table table-bordered table-bordered2 text-center">
                              <?php
                                  $filter_page_count = PAGE_COUNT;
                                  $link = connect();
                                  $start = ($page-1) * $filter_page_count;
                                  $limit = " LIMIT $start,".$filter_page_count;
                                  $res = query(TABLE_PRODUCTS,[]," WHERE status=".STATUS_SELLING." and user_id=".$_SESSION['user_id']." ORDER BY `$sortby` $sorttype",$link,$limit);
                                  while($row = mysqli_fetch_array($res)){
                                    $price_jppp = $filter_amazon_prime=='1' ? $row['price_jp_prime'] : $row['price_jp'];
                                    $days_to_ship = $filter_amazon_prime=='1' ? $row['days_to_ship_prime'] : $row['days_to_ship'];
                              ?>
                              <tr<?php echo isset($blacklist_asins[$row['asin']]) || blacklisted($row['commodity'],$blacklist_keywords) ? " style='background-color:lightcoral'" : "" ?>>
                                <td valign="middle"  class="middle-text" style="width:6%;font-size:12px"><?php echo $row['selling_date'];?></td>
                                <td  class="middle-text" style="width:6%"><img style="max-width:50px;height:75px" src="<?php echo $row['image_us'];?>"  alt=""/></td>
                                <td  class="middle-text" style="width:6%"><img style="max-width:50px;height:75px" src="<?php echo $row['image_jp'];?>"  alt=""/></td>
                                <td class="break-span color-blue" style="width: 6%;">
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>"><?php echo $row['asin'];?> (JP)</a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>"><?php echo $row['asin'];?> (US)</a></span>
                                </td>
                                <td class="break-span color-blue" style="width:6%">
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>"><?php echo $row['commodity']!="" && strlen($row['commodity'])>16 ? substr($row['commodity'],0,15)."..." : $row['commodity'];?></a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>"><?php echo $row['commodity']!="" && strlen($row['commodity'])>16 ? substr($row['commodity'],0,15)."..." : $row['commodity'];?></a></span>
                                </td>
                                <td style="vertical-align: middle;width:5%">#<?php echo $row['ranking'];?></td>
                                <?php
                                  $expected_profit = round(($row['price_us']*US_JP_RATE) - $price_jppp - ($row['shipping']) - $row['amazon_commission']);
                                ?>
                                <td style="vertical-align: middle;width:5%;<?php echo $expected_profit>0?'color:blue':'color:red' ?>">¥<?php echo $expected_profit ?></td>
                                <td class="break-span color-blue" style="width:5%;" class="tooltip-r" data-toggle="tooltip" data-placement="right" title="<?php echo get_prices3_html($row['prices_3_us'],'$');?>">
                                  <span class="default-color"><?php echo $row['price_us']==0 ? "出品無し" : "¥".round($row['price_us'] * US_JP_RATE);?></span>
                                  <span class="default-color"><?php echo $row['price_us']==0 ? "" : "$".round($row['price_us'],2);?></span>
                                </td>
                                <td class="break-span color-blue" style="width:5%;">
                                  <span class="default-color"><?php echo $row['price_us']==0 ? "出品無し" : "¥".round($row['price_us'] * US_JP_RATE);?></span>
                                  <span class="default-color"><?php echo $row['price_us']==0 ? "" : "$".round($row['price_us'],2);?></span>
                                </td>
                                <td valign="middle" class="middle-text" style="width:5%;" class="tooltip-r" data-toggle="tooltip" data-placement="right" title="<?php echo get_prices3_html($row['prices_3_jp'],'¥');?>"><?php echo $price_jppp==0 ? "在庫なし" : "¥".round($price_jppp);?></td>
                                <td class="largebreak" style="width:6%;">
                                  <?php $unit = strpos($row['weight'], "pound")!==false ? LB_to_GM : (strpos($row['weight'], "ounces")!==false ? OZ_to_GM : (strpos($row['weight'], "Kg")!==false ? KG_to_GM : 1)) ;?>
                                    <span><?php echo $row['weight']!="" ? round($row['weight']*$unit)." g" : "NONE";?></span>
                                    <?php
                                      $total_dim = "";
                                      $dim1='';
                                      $dim2='';
                                      $dim3='';
                                      if($row['dimensions']!=""){
                                        preg_match('/([0-9\.]+) x ([0-9\.]+) x ([0-9\.]+)/si',$row['dimensions'],$dim_matches);
                                        $dim1 = stripos($row['dimensions'],"inch")!==false ? round($dim_matches[1] * INCH_to_CM,1) : round($dim_matches[1],1);
                                        $dim2 = stripos($row['dimensions'],"inch")!==false ? round($dim_matches[2] * INCH_to_CM,1) : round($dim_matches[2],1);
                                        $dim3 = stripos($row['dimensions'],"inch")!==false ? round($dim_matches[3] * INCH_to_CM,1) : round($dim_matches[3],1);
                                        $total_dim = $dim1+$dim2+$dim3;
                                      }
                                    ?>
                                    <span><?php echo $total_dim; ?></span>
                                    <span style="font-size:12px"><?php echo $dim1!="" ? "$dim1 x $dim2 x $dim3" : "";?></span>
                                </td>
                                <td style="vertical-align: middle;width:5%;">¥<?php echo round($row['shipping']);?></td>
                                <td class="break-span color-blue" style="width:5%;">
                                  <?php
                                    $amazon_commission = $row['price_us']>0 ? (($row['price_us']*US_JP_RATE)) * 0.15 : 0;
                                  ?>
                                  <span class="default-color">¥<?php echo round($amazon_commission);?>&nbsp;</span>
                                  <span class="default-color">$<?php echo round($amazon_commission/US_JP_RATE,2)?>&nbsp;</span>
                                </td>
                                <td class="break-span color-blue" style="width:8%;">
                                <span class="default-color"><?php echo $row['release_date'];?>&nbsp;</span>
                                <span class="default-color"><?php echo $row['sold_numbers'];?>&nbsp;</span>
                                </td>
                                <td class="break-span color-blue" style="width:6%;">
                                <span class="default-color"><?php echo $row['num_of_sellers'];?></span>
                                <span class="default-color"><?php echo $days_to_ship;?></span>
                                </td>
                                <td class="break-span color-blue" style="width:5%;">
                                  <!-- <a target="_blank" href="compare.php?asin=<?php echo $row['asin'];?>">Compare</a> -->
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>">Compare (JP)</a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>">Compare (US)</a></span>
                                </td>
                                <td  class="middle-text" style="width:5%;"><input type="checkbox" class="delete" name="delete[]" value="<?php echo $row['asin'] ?>"></td>
                                <td  class="middle-text" style="width:5%;"><input type="checkbox" class="bl" name="bl[]" value="<?php echo $row['asin'] ?>"></td>
                              </tr>
                              <?php } ?>
                            </table>
                            
                       </div>
                   </div>
                                      
                   <?php 
                    $res_count = query(TABLE_PRODUCTS,[]," WHERE status=".STATUS_SELLING." and user_id=".$_SESSION['user_id'],$link);
                    include("pagination.php");
                   ?>
                </div>
                
                
                
                    
                </div>
            </div>
          </form>
        </div>
        </div>

        <!-- Content END --> 
        
        <?php include("footer.php"); ?>

        </div>
    </div>
    <?php include("scripts.php"); ?>
    <script>$('.tooltip-r').tooltip();</script>
</body>

</html>
