<?php
    require_once("backend/db.php");
    require_once("backend/extraction.php");
    //
    authenticate_user();
    //
    $page = get_param("page",1);
    //
    $link = connect();
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
    // Progress
    $res_new_count = query(TABLE_PRODUCTS,[]," WHERE status=".STATUS_NEW." and user_id=".$_SESSION['user_id'].$where_sql,$link);
    $progress_new_count = mysqli_num_rows($res_new_count);
    // Sort
    $sortby = get_param("sortby","updated");
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
      var sort_fields = ["ranking","expected_profit","price_us",price_field];
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
            <div class="container-fluid">
                <div class="section-operator asin-isbn">
                <div class="top-btn">
                  <form action="extraction.php" method="post" enctype="multipart/form-data" name="Form1">
                    <input type="hidden" id="country_field" name="country" value="<?php echo $country;?>"/>
                    <input type="hidden" id="cmd1" name="cmd" value="IMPORT"/>
                     <div class="col-md-12">
                      <div class="row">
                   <div class="col-md-2">
                    <div class="country-select">
                    <div class="dropdown">
                    <button class="btn btn-block btn-default dropdown-toggle" type="button" data-toggle="dropdown"><span id="country_label"><?php echo $country;?></span>
                    <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                    <li><a href="javascript:{}" onclick="$('#country_label').html('USA');$('#country_field').val('USA');$('#cmd1').val('SORT');document.forms.Form1.submit();">USA</a></li>
                    <li><a href="javascript:{}" onclick="$('#country_label').html('CANADA');$('#country_field').val('CANADA');$('#cmd1').val('SORT');document.forms.Form1.submit();">CANADA</a></li>
                    </ul>
                    </div>
                    </div>
                    </div>
                     
                     <div class="col-md-4">
                        <label>CSV Import</label>
                        <button class="btn btn-sm btn-primary" onclick="$('#file-upload').trigger('click');return false;">File</button>
                        <input id="file-upload" name="csv_file" type="file" style="display:none"/>
                        <button class="btn btn-sm btn-primary" onclick="$('#cmd1').val('IMPORT');document.forms.Form1.submit();">Import</button>
                        <font><?php echo $import_file_name; ?></font>&nbsp;
                        <font color="red"><?php echo $progress_new_count > 0 ? "In Queue: ".$progress_new_count : ""; ?></font>
                     </div>
                     <div class="col-md-3">
                        <label>ASIN Import</label>
                        <input name="asin_import" size="10" type="text"/>
                        <button class="btn btn-sm btn-primary" onclick="$('#cmd1').val('IMPORT2');document.forms.Form1.submit();">Import</button>
                     </div>
                     
                     <div class="col-md-3">
                     <div class="ad-top">
                            <span> <a target="_blank" href="http://kurofune.club"><img src="img/hp-btn.png" alt="" ></a></span>
                             <span> <a target="_blank" href="https://sellercentral.amazon.com/gp/homepage.html"><img src="img/amazon-ad.png" alt="" /></a></span>
                        </div>
                     </div>
                     
                     </div>
                 </div>
                 </form>
                 </div>
                <form action="extraction.php" method="post" name="Form2">
                  <input type="hidden" id="country_field2" name="country" value="<?php echo $country;?>"/>
                  <input type="hidden" name="cmd" id="cmd" value=""/>
                  <input type="hidden" name="page" id="page" value=""/>
                  <input type="hidden" name="sortindex" id="sortindex" value="<?php echo $sortindex; ?>"/>
                  <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>"/>
                  <input type="hidden" name="sorttype" id="sorttype" value="<?php echo $sorttype; ?>"/>
                <div class="top-small-form">
                    <div class="col-md-12">
                        <div class="main-column">
                            <div class="small-form-group">
                                <label>Extraction Condition</label>
                                <?php $filter_category = get_param("filter_category"); ?>
                                 <select name="filter_category">
                                   <option value="">ALL</option>
                                    <option value="BOOK"<?php echo $filter_category=='BOOK' ? " selected" : ""?>>BOOK</option>
                                    <option value="WESTERN BOOK"<?php echo $filter_category=='WESTERN BOOK' ? " selected" : ""?>>WESTERN BOOK</option>
                                    <option value="MUSIC"<?php echo $filter_category=='MUSIC' ? " selected" : ""?>>MUSIC</option>
                                    <option value="DVD"<?php echo $filter_category=='DVD' ? " selected" : ""?>>DVD</option>
                                    <option value="TOYS"<?php echo $filter_category=='TOYS' ? " selected" : ""?>>TOYS</option>
                                    <option value="HOBBY"<?php echo $filter_category=='HOBBY' ? " selected" : ""?>>HOBBY</option>
                                    <option value="TV GAMES"<?php echo $filter_category=='TV GAMES' ? " selected" : ""?>>TV GAMES</option>
                                    <option value="PC SOFTWARES"<?php echo $filter_category=='PC SOFTWARES' ? " selected" : ""?>>PC SOFTWARES</option>
                                    <option value="PC APPLIANCES"<?php echo $filter_category=='PC APPLIANCES' ? " selected" : ""?>>PC APPLIANCES</option>
                                    <option value="Home appliances & CAMERA"<?php echo $filter_category=='Home appliances & CAMERA' ? " selected" : ""?>>Home appliances & CAMERA</option>
                                    <option value="STATIONARY"<?php echo $filter_category=='STATIONARY' ? " selected" : ""?>>STATIONARY</option>
                                    <option value="HOME & KITCHIN"<?php echo $filter_category=='HOME & KITCHIN' ? " selected" : ""?>>HOME & KITCHIN</option>
                                    <option value="PET GOODS"<?php echo $filter_category=='PET GOODS' ? " selected" : ""?>>PET GOODS</option>
                                    <option value="BEATY GOODS"<?php echo $filter_category=='BEATY GOODS' ? " selected" : ""?>>BEATY GOODS</option>
                                    <option value="BABY & MATTERNITY"<?php echo $filter_category=='BABY & MATTERNITY' ? " selected" : ""?>>BABY & MATTERNITY</option>
                                    <option value="MUSICAL INSTRUMENTS"<?php echo $filter_category=='MUSICAL INSTRUMENTS' ? " selected" : ""?>>MUSICAL INSTRUMENTS</option>
                                    <option value="SPORT & OUTDOORS"<?php echo $filter_category=='SPORT & OUTDOORS' ? " selected" : ""?>>SPORT & OUTDOORS</option>
                                    <option value="CAR, BIKE GOODS"<?php echo $filter_category=='CAR, BIKE GOODS' ? " selected" : ""?>>CAR, BIKE GOODS</option>
                                    <option value="DIY, TOOLS"<?php echo $filter_category=='DIY, TOOLS' ? " selected" : ""?>>DIY, TOOLS</option>
                                    <option value="WATCH"<?php echo $filter_category=='WATCH' ? " selected" : ""?>>WATCH</option>
                                    <option value="FASHION"<?php echo $filter_category=='FASHION' ? " selected" : ""?>>FASHION</option>
                                    <option value="DRUG STORE"<?php echo $filter_category=='DRUG STORE' ? " selected" : ""?>>DRUG STORE</option>
                                    <option value="Industrial and research supplies"<?php echo $filter_category=='Industrial and research supplies' ? " selected" : ""?>>Industrial and research supplies</option>
                                 </select>
                            </div>
                        </div>
                        
                        <div class="main-column">
                            <div class="small-form-group">
                                <label>Profit</label>
                                <?php $filter_profit = get_param("filter_profit"); ?>
                                 <input type="text" name="filter_profit" value="<?php echo $filter_profit; ?>">
                                 <span class="label-right">more than</span>
                            </div>
                        </div>
                        
 
                        <div class="main-column">
                            <div class="small-form-group">
                                <label>purchase Price</label>
                                <?php $filter_price = get_param("filter_price"); ?>
                                 <input type="text" name="filter_price" value="<?php echo $filter_price; ?>">
                                 <span class="label-right">less than</span>
                            </div>
                        </div>
                        
 
                        <div class="main-column">
                            <div class="small-form-group">
                                <label>numbers of sellers</label>
                                <?php $filter_num_of_sellers = get_param("filter_num_of_sellers"); ?>
                                 <input type="text" name="filter_num_of_sellers" value="<?php echo $filter_num_of_sellers; ?>">
                                 <span class="label-right">less than</span>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </div>
                
                
                <div class="top-btn2">
                    <div class="col-md-12">
                      <div class="row">
                    <div class="col-md-10">
                    <button class="btn btn-sm btn-primary m-r-10" onclick="document.getElementById('cmd').value='SORT';document.forms.Form2.submit();">Sort</button>
                     <button class="btn btn-sm btn-primary m-r-10" onclick="document.getElementById('cmd').value='CLEAR ACTION';document.forms.Form2.submit();">Clear Action</button>
                      <label class="m-r-10">Amazon Prime <input type="checkbox" name="filter_amazon_prime" value="1"<?php echo $filter_amazon_prime=='1' ? ' checked':'';?>> </label>
                      <label class="m-r-10">Exclude NO JP <input type="checkbox" name="exclude_no_jp" value="1"<?php echo $exclude_no_jp=='1' ? ' checked':'';?>> </label>
                       <label>Checked commodity <button class="btn btn-sm btn-primary" onclick="document.getElementById('cmd').value='EXECUTE ACTION';document.forms.Form2.submit();">EXCUTE ACTION</button></label>
                       &nbsp;&nbsp;&nbsp;<button class="btn btn-sm btn-primary" onclick="if(confirm('Are you sure you want to DELETE ALL Products?')){document.getElementById('cmd').value='DELETE ALL';document.forms.Form2.submit();}">DELETE ALL</button>
                     </div>
                     
                     </div>
                     
                    </div>
                </div>
                <div class="top-btn2">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-8">
                          <div class="main-column">
                            <div class="small-form-group">
                              <?php $filter_page_count = get_param("filter_page_count",PAGE_COUNT); ?>
                              <label>ASINs</label>
                              <select name="filter_page_count" style="width:100px;" onchange="document.forms.Form2.submit();">
                                <option value="20"<?php echo $filter_page_count==20 ? " selected" : ""?>>20</option>
                                <option value="50"<?php echo $filter_page_count==50 ? " selected" : ""?>>50</option>
                                <option value="100"<?php echo $filter_page_count==100 ? " selected" : ""?>>100</option>
                              </select>
                            </div>
                          </div>
                        </div>
                     </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                  <div class="selling-section large-table-scroll2">
                       <div class="table-responsive">
                          <table  class="table table-bordered table-bordered2 text-center dataTable" style="width:98.8%;margin-bottom:0px;">
                              <tr>
                                <th colspan="2"  class="middle-text" valign="middle" style="width: 12%;">Image</th>
                                <th valign="middle" class="middle-text" style="width: 8%;">ASIN/ISBN</th>
                                <th valign="middle" class="middle-text"  style="width: 8%;">Commodity</th>
                                <th valign="middle" class="middle-text sorting"  style="width: 5%;background: url('img/sort_<?php echo $sortby!=""&&$sortby=='ranking'? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(0);">Ranking</a></th>
                                <th valign="middle" class="middle-text sorting"  style="width: 6%;background: url('img/sort_<?php echo $sortby!=""&&$sortby=='expected_profit'? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(1);">Expected<br>Profit</a></th>
                                <th valign="middle" class="middle-text sorting"   style="width: 6%;background: url('img/sort_<?php echo $sortby!=""&&$sortby=='price_us' ? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(2);">US Price<br>(Sell)</a></th>
                                <th valign="middle" class="middle-text sorting" style="width: 6%;background: url('img/sort_<?php echo $sortby!=""&&($sortby=='price_jp'||$sortby=='price_jp_prime')? strtolower($sorttype) : 'none' ?>.png') no-repeat center right;"><a href="javascript:{}" onclick="sort(3);">JP Price<br>(Purchase)</a></th>
                                <th valign="middle"class="middle-text break-span"  style="width: 6%;"><SPAN>Weight</SPAN><SPAN>Dim.</SPAN></th>
                                <th valign="middle"class="middle-text"  style="width: 6%;">Shipping Rate</th>
                                <th valign="middle" class="middle-text" style="width: 6%;">Amazon<br>Commission</th>
                                <th valign="middle"class="middle-text break-span"  style="width: 7%;"><SPAN>Release date</SPAN><SPAN style="font-size:12px">Sold Numbers (30 Days)</SPAN></th>
                                <th valign="middle" class="middle-text break-span"   style="width:6%;font-size:12px"><SPAN>Sellers Number</SPAN><SPAN>Expected Arrival</SPAN></th>
                                <th valign="middle" class="middle-text"   style="width: 5%;">Compare</th>
                                <th valign="middle" class="middle-text" style="width: 5%;">START<br>SELLING<br><input type="checkbox" onclick="$(':checkbox[class=\'start_selling\']').trigger('click')"/></th>
                                <th valign="middle" class="middle-text" style="width: 5%;">Delete<br><input type="checkbox" onclick="$(':checkbox[class=\'delete\']').trigger('click')"/></th>
                                <th valign="middle" class="middle-text" style="width: 5%;">BL<br><input type="checkbox" onclick="$(':checkbox[class=\'bl\']').trigger('click')"/></th>
                              </tr>
                          </table>
                        </div>
                    </div>
                   <div class="selling-section large-table-scroll">
                       <div class="table-responsive">
                           <table  class="table table-bordered table-bordered2 text-center">
                              <?php
                                  $link = connect();
                                  $start = ($page-1) * $filter_page_count;
                                  $limit = " LIMIT $start,".$filter_page_count;
                                  //
                                  $res = query(TABLE_PRODUCTS,[]," WHERE status=".STATUS_DEFAULT." and user_id=".$_SESSION['user_id'].$where_sql." ORDER BY `$sortby` $sorttype",$link,$limit);
                                  while($row = mysqli_fetch_array($res)){
                                    $price_jppp = $filter_amazon_prime=='1' ? $row['price_jp_prime'] : $row['price_jp'];
                                    $days_to_ship = $filter_amazon_prime=='1' ? $row['days_to_ship_prime'] : $row['days_to_ship'];
                              ?>
                              <tr<?php echo isset($blacklist_asins[$row['asin']]) || blacklisted($row['commodity'],$blacklist_keywords) ? " style='background-color:lightcoral'" : "" ?>>
                                <td  class="middle-text" style="width:6%"><img style="max-width:50px;height:75px" src="<?php echo $row['image_us'];?>"  alt=""/></td>
                                <td  class="middle-text" style="width:6%"><img style="max-width:50px;height:75px" src="<?php echo $row['image_jp'];?>"  alt=""/></td>
                                <td class="break-span color-blue" style="width: 8%;">
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>"><?php echo $row['asin'];?> (JP)</a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>"><?php echo $row['asin'];?> (US)</a></span>
                                </td>
                                <td class="break-span color-blue" style="width:8%">
                                  <span><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $row['asin'];?>?SubscriptionId=<?php echo GLOBAL_COMP_AWS_ACCESS_KEY_ID;?>&tag=gcomp00-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=<?php echo $row['asin'];?>"><?php echo $row['commodity']!="" && strlen($row['commodity'])>16 ? substr($row['commodity'],0,15)."..." : $row['commodity'];?></a></span>
                                  <span><a target="_blank" href="http://www.amazon.com/dp/<?php echo $row['asin'];?>"><?php echo $row['commodity']!="" && strlen($row['commodity'])>16 ? substr($row['commodity'],0,15)."..." : $row['commodity'];?></a></span>
                                </td>
                                <td style="vertical-align: middle;width:5%">#<?php echo $row['ranking'];?></td>
                                <?php
                                  $amazon_commission = ($row["price_us"]*US_JP_RATE) * 0.15;
                                  $expected_profit = round(($row["price_us"]*US_JP_RATE) - $price_jppp - ($row['shipping']) - $amazon_commission);
                                ?>
                                <td style="vertical-align: middle;width:6%;<?php echo $expected_profit>0?'color:blue':'color:red' ?>">¥<?php echo $expected_profit ?></td>
                                <td class="break-span color-blue" style="width:6%;" class="tooltip-r" data-toggle="tooltip" data-placement="right" title="<?php echo get_prices3_html($row['prices_3_us'],'$');?>">
                                <span class="default-color"><?php echo $row["price_us"]==0 ? "出品無し" : "¥".round($row["price_us"] * US_JP_RATE);?></span>
                                <span class="default-color"><?php echo $row["price_us"]==0 ? "" : "$".round($row["price_us"],2);?></span>
                                </td>
                                <td valign="middle" class="middle-text" style="width:6%;" class="tooltip-r" data-toggle="tooltip" data-placement="right" title="<?php echo get_prices3_html($row['prices_3_jp'],'¥');?>"><?php echo $price_jppp==0 ? "在庫なし" : "¥".round($price_jppp);?></td>
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
                                <td class="<?php echo ($row['weight']!='' || $dim1!='') ? 'largebreak':'';?>" style="width:6%;">
                                  <?php $unit = strpos($row['weight'], "pound")!==false ? LB_to_GM : (strpos($row['weight'], "ounces")!==false ? OZ_to_GM : (strpos($row['weight'], "Kg")!==false ? KG_to_GM : 1)) ;?>
                                    <span><?php echo $row['weight']!="" ? round($row['weight']*$unit)." g" : "NONE";?></span>
                                    <span><?php echo $total_dim; ?></span>
                                    <span style="font-size:12px"><?php echo $dim1!="" ? "$dim1 x $dim2 x $dim3" : "";?></span>
                                </td>
                                <td style="vertical-align: middle;width:6%;">¥<?php echo round($row['shipping']);?></td>
                                <td class="break-span color-blue" style="width:6%;">
                                  <span class="default-color">¥<?php echo round($amazon_commission);?>&nbsp;</span>
                                  <span class="default-color">$<?php echo round($amazon_commission/US_JP_RATE,2)?>&nbsp;</span>
                                </td>
                                <td class="break-span color-blue" style="width:7%;">
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
                                 <td  class="middle-text" style="width:5%;"><input type="checkbox" class="start_selling" name="start_selling[]" value="<?php echo $row['asin'] ?>"></td>
                                <td  class="middle-text" style="width:5%;"><input type="checkbox" class="delete" name="delete[]" value="<?php echo $row['asin'] ?>"></td>
                                <td  class="middle-text" style="width:5%;"><input type="checkbox" class="bl" name="bl[]" value="<?php echo $row['asin'] ?>"></td>
                              </tr>
                              <?php } ?>
                             
                            </table>
                            
                       </div>
                   </div>
                   
                   <?php 
                    $res_count = query(TABLE_PRODUCTS,[]," WHERE status=".STATUS_DEFAULT." and user_id=".$_SESSION['user_id'].$where_sql,$link);
                    include("pagination.php");
                   ?>
                </div>
                </form>
                
                
                    
                </div>
            </div>

        </div>
        <!-- Content END --> 
        
        <?php include("footer.php"); ?>

        </div>
    </div>
    <?php include("scripts.php"); ?>
    <script>$('.tooltip-r').tooltip();</script>
    <?php
      if($page>1)
        echo "<script>window.scroll(0,10000)</script>";
    ?>
</body>

</html>
