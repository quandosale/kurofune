<?php
    require_once("backend/db.php");
    require_once("backend/settings.php");
    //
    authenticate_user();
    //
    $page = get_param("page",1);
    //
    $link = connect();
    $res = query(TABLE_SETTINGS,[]," WHERE user_id=".$_SESSION['user_id'],$link);
    $row1 = mysqli_fetch_array($res);
    $row2 = mysqli_fetch_array($res);
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
      <form method="post" action="settings.php">
        <input type="hidden" name="cmd" value="SAVE"/>
            <div class="container-fluid">
                <div class="section-operator asin-isbn">
                <div class="top-btn">
                     <div class="col-md-12">
                     <div class="col-md-3 pull-right">
                     <div class="ad-top">
                            <span> <a target="_blank" href="http://kurofune.club"><img src="img/hp-btn.png" alt="" ></a></span>
                             <span> <a target="_blank" href="https://sellercentral.amazon.com/gp/homepage.html"><img src="img/amazon-ad.png" alt="" /></a></span>
                        </div>
                     </div>
                     </div>
                 </div>
               <!-- Start top-form-->
               <div class="top-form">
                  <div class="col-md-10">
                    <fieldset>
                    <legend><B>SELLER INFORMATION</B></legend>
                    <div class="col-md-4">
                      <div class="form-group clearfix">
                        <label class="col-md-5">Seller ID (US)</label>
                        <div class="col-md-7">
                         <input class="form-control" type="text" name="seller_id[]" value="<?php echo $row1["seller_id"] ?>">
                        </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group clearfix">
                        <label class="col-md-5">Access Key (US)</label>
                        <div class="col-md-7">
                         <input class="form-control" type="text" name="access_key[]" value="<?php echo $row1["access_key"] ?>">
                        </div>
                         </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group clearfix">
                        <label class="col-md-5">Secret Key (US)</label>
                        <div class="col-md-7">
                         <input class="form-control" type="text" name="secret_key[]" value="<?php echo $row1["secret_key"] ?>">
                        </div>
                         </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group clearfix">
                        <label class="col-md-5">Seller ID (CA)</label>
                        <div class="col-md-7">
                         <input class="form-control" type="text">
                        </div>
                         </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group clearfix">
                        <label class="col-md-5">Access Key (CA)</label>
                        <div class="col-md-7">
                         <input class="form-control" type="text">
                        </div>
                         </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group clearfix">
                        <label class="col-md-5">Secret Key (CA)</label>
                        <div class="col-md-7">
                         <input class="form-control" type="text">
                        </div>
                          </div>
                    </div>
                    </fieldset>
                    </div>
               </div>
               <!-- End top-form-->
                <div class="col-md-12" style="margin-top:10px">
                   <div class="setting-form">
                        <div class="setting-tab">
                        <ul class="nav nav-pills nav-tabs">
                          <li class="active"><a data-toggle="pill" href="#home">USA</a></li>
                          <li><a data-toggle="pill" href="#menu1">CANADA</a></li>
                        </ul>
                        <div class="tab-content">
                          <div id="home" class="tab-pane fade in active">
                            <fieldset style="margin-bottom:10px">
                            <legend><B>SHIPPING RATE</B></legend>
                             <div class="form-column">
                                <!-- <h3 class="form-head">SHIPPING RATE</h3> -->
                                <div class="row">
                                <div class="col-md-6">
                                 <fieldset>
                                  <legend>Category 1</legend>
                                     <div class="form-group">
                                        <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="standard_type1[]" value="SAL"<?php echo $row1["standard_type"]=="SAL" ? " checked" : "" ?>>SAL  </label>
                                        <label class="radio-inline"><input type="radio" name="standard_type1[]" value="E Bucket"<?php echo $row1["standard_type"]=="E Bucket" ? " checked" : "" ?>>E Bucket  </label>
                                        <label class="radio-inline"><input type="radio" name="standard_type1[]" value="EMS"<?php echo $row1["standard_type"]=="EMS" ? " checked" : "" ?>>EMS  </label>
                                        <label class="radio-inline"><input type="radio" name="standard_type1[]" value="D mail"<?php echo $row1["standard_type"]=="D mail" ? " checked" : "" ?>>D Mail  </label>
                                        </div>
                                     </div>
                                     <p>Less than 2kg & Less than 80 cm in total & Less than 60 cm at the longest side</p>
                                     <div class="col-md-12">
                                          <div class="row">
                                         <div class="form-group row clearfix">
                                        <label class="col-md-4">Handling comm. per article  </label>
                                        <div class="col-md-8">
                                           <div class="text-symbol">
                                           <span class="currency-icon">¥</span>  <input class="form-control" type="text" name="commission[]" value="<?php echo $row1["commission"]?>">
                                           </div>
                                        </div>
                                        </div>
                                        </div>
                                     </div>
                                 </fieldset>
                                </div>
                                <div class="col-md-6">
                                 <fieldset>
                                  <legend>Category 2</legend>
                                     <div class="form-group">
                                        <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="out_standard_type1[]" value="SAL"<?php echo $row1["out_standard_type"]=="SAL" ? " checked" : "" ?>>SAL  </label>
                                        <label class="radio-inline"><input type="radio" name="out_standard_type1[]" value="EMS"<?php echo $row1["out_standard_type"]=="EMS" ? " checked" : "" ?>>EMS  </label>
                                        </div>
                                     </div>
                                     <p>More than 2kg or More than 80 cm in total & More than 60 cm at the longest side</p>
                                     <div class="col-md-12">
                                          <div class="row">
                                         <div class="form-group row clearfix">
                                        <label class="col-md-4">Handling comm. per article  </label>
                                        <div class="col-md-8">
                                           <div class="text-symbol">
                                           <span class="currency-icon">¥</span>  <input class="form-control" type="text" name="out_commission[]" value="<?php echo $row1["out_commission"]?>">
                                           </div>
                                        </div>
                                        </div>
                                        </div>
                                     </div>
                                 </fieldset>
                                </div>
                                </div>
                                </div>
                                <div class="form-column">
                                <div class="row">
                                <div class="col-md-6">
                                 <fieldset>
                                  <legend>Category 3</legend>
                                     <p>music/DVD/Bluelay.PC game/TV game</p>
                                     <div class="col-md-12">
                                          <div class="row">
                                         <div class="form-group row clearfix custop-item-space">
                                        <label class="col-md-4">Shipping rate per item</label>
                                        <div class="col-md-8">
                                           <div class="text-symbol">
                                           <span class="currency-icon">¥</span>  <input class="form-control" type="text" name="others_shipping_rate[]" value="<?php echo $row1["others_shipping_rate"]?>">
                                           </div>
                                        </div>
                                        </div>
                                        </div>
                                     </div>
                                 </fieldset>
                                </div>
                                <div class="col-md-6">
                                 <fieldset>
                                  <legend>Category 4</legend>
                                     <p>No Weight Discription</p>
                                     <div class="col-md-12">
                                          <div class="row">
                                         <div class="form-group row clearfix custop-item-space">
                                        <label class="col-md-4">Shipping rate per item</label>
                                        <div class="col-md-8">
                                           <div class="text-symbol">
                                           <span class="currency-icon">¥</span>  <input class="form-control" type="text" name="others2_shipping_rate[]" value="<?php echo $row1["others2_shipping_rate"]?>">
                                           </div>
                                        </div>
                                        </div>
                                        </div>
                                     </div>
                                 </fieldset>
                                </div>
                                </div>
                             </div>
                           </fieldset>
                             
               <div class="form-column">
                    <div class="row">
                    <div class="col-md-12">
                     <fieldset style="margin-bottom:10px">
                      <legend><B>PACKAGE WEIGHT</B></legend>
                      <div class="rownew">
                      <div class="col-md-4">
                          <div class="row">
                        <div class="form-group">
                            <label class="col-md-6 label-custom"> COMMODITY WEIGHT <small>LESS THAN 500G</small>    </label>
                            <div class="col-md-6">
                                <div class="right-icon-text">
                                  <span class="right-text-icon">g</span>  <input type="text"  class="form-control" placeholder="Enter" name="weight_less[]" value="<?php echo $row1["weight_less"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                       </div>  
                       <div class="col-md-4">
                          <div class="row">
                        <div class="form-group">
                            <label class="col-md-6 label-custom"> COMMODITY WEIGHT  <small>between 501g and 1001g</small>    </label>
                            <div class="col-md-6">
                                <div class="right-icon-text">
                                  <span class="right-text-icon">g</span>  <input type="text"  class="form-control" placeholder="Enter" name="weight_between[]" value="<?php echo $row1["weight_between"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                       </div>
                       <div class="col-md-4">
                          <div class="row">
                        <div class="form-group">
                            <label class="col-md-6 label-custom"> COMMODITY WEIGHT <small>more than 1001g</small>    </label>
                            <div class="col-md-6">
                                <div class="right-icon-text">
                                  <span class="right-text-icon">g</span>  <input type="text"  class="form-control" placeholder="Enter" name="weight_more[]" value="<?php echo $row1["weight_more"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                       </div>
                      </div>
                    </fieldset>
                    <fieldset style="margin-bottom:10px">
                    <legend><B>SALE CONDITION</B></legend>
                   <div class="col-md-7">
                       <div class="section-new-table">
                        <div class="table-responsive">
              <table class="table table-bordered" width="100%">
              <tbody><tr>
                <td width="22%">Condition Note</td>
                <!-- <input type="text" class="form_control" name="note[]" style="width:500px" value="<?php echo $row1["note"]?>"/> -->
                <td colspan="3" align="left" style="text-align:left;" class="color-light">
                  <textarea name="note[]" rows="2" cols="60"><?php echo $row1["note"]!="" ? $row1["note"] : 'Brand new.The commodity will be shipped by Japan Speed Post EMS.Purchased commodity will arrive at your home in approx 5 days.'; ?></textarea></td>
              </tr>
              <tr>
                <td>Handling Time</td>
                <td width="25%">
                <select class="form-control" name="handling_time[]">
                  <option value="3"<?php echo $row1["handling_time"]=="3" ? " selected" : "" ?>>3 days</option>
                    <option value="5"<?php echo $row1["handling_time"]=="5" ? " selected" : "" ?>>5 days</option>
                    <option value="10"<?php echo $row1["handling_time"]=="10" ? " selected" : "" ?>>10 days</option>
                </select>
                </td>
                <td style="text-align:center" width="18%">出品個数</td>
                <td width="25%">
                <select class="form-control" name="num_of_articles[]">
                  <option value="1"<?php echo $row1["num_of_articles"]=="1" ? " selected" : "" ?>>1</option>
                    <option value="2"<?php echo $row1["num_of_articles"]=="2" ? " selected" : "" ?>>2</option>
                    <option value="3"<?php echo $row1["num_of_articles"]=="3" ? " selected" : "" ?>>3</option>
                    <option value="4"<?php echo $row1["num_of_articles"]=="4" ? " selected" : "" ?>>4</option>
                    <option value="5"<?php echo $row1["num_of_articles"]=="5" ? " selected" : "" ?>>5</option>
                    <option value="6"<?php echo $row1["num_of_articles"]=="6" ? " selected" : "" ?>>6</option>
                    <option value="7"<?php echo $row1["num_of_articles"]=="7" ? " selected" : "" ?>>7</option>
                    <option value="8"<?php echo $row1["num_of_articles"]=="8" ? " selected" : "" ?>>8</option>
                    <option value="9"<?php echo $row1["num_of_articles"]=="9" ? " selected" : "" ?>>9</option>
                    <option value="10"<?php echo $row1["num_of_articles"]=="10" ? " selected" : "" ?>>10</option>
                </select>
                </td>
              </tr>
            </tbody>
            </table>
              </div>
                       </div>
                   </div>  
                     </fieldset>
                    </div>
                    </div>
                 </div>
                 <fieldset style="margin-bottom:10px">
                    <legend><B>SALE PRICE SETTING</B></legend>
                <div class="form-column">
                    <div class="row">
                    <div class="col-md-12">
                     <fieldset>
                      <legend>Setting 01</legend>
                      <div class="rownew">
                      <div class="col-md-2">
                         <p>Selling Price Adjustment</p>
                      </div>
                      <div class="col-md-8">
                      <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 label-custom"><input type="radio" name="profit_type1[]" value="amount"<?php echo $row1["profit_type"]=="amount" ? " checked" : "" ?>> profit amount    </label>
                            <div class="col-md-2">
                                <div class="right-icon-text2">
                                  <span class="right-text-icon">Yen</span>  <input class="form-control" placeholder="Enter" type="text" name="profit_amount_from[]" style="width:90px;" value="<?php echo $row1["profit_amount_from"]?>">
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                               <strong>~</strong>
                            </div>
                            <div class="col-md-2">
                                <div class="right-icon-text2">
                                  <span class="right-text-icon">Yen</span>  <input class="form-control" placeholder="Enter" type="text" name="profit_amount_to[]" style="width:90px;" value="<?php echo $row1["profit_amount_to"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 label-custom"><input type="radio" name="profit_type1[]" value="rate"<?php echo $row1["profit_type"]=="rate" ? " checked" : "" ?>> profit rate    </label>
                            <div class="col-md-2">
                                <div class="right-icon-text2">
                                  <span class="right-text-icon">%</span>  <input class="form-control" placeholder="Enter" type="text" name="profit_rate_from[]" style="width:90px;" value="<?php echo $row1["profit_rate_from"]?>">
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                               <strong>~</strong>
                            </div>
                            <div class="col-md-2">
                                <div class="right-icon-text2">
                                  <span class="right-text-icon">%</span>  <input class="form-control" placeholder="Enter" type="text" name="profit_rate_to[]" style="width:90px;" value="<?php echo $row1["profit_rate_to"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                       </div>  
                      </div>
                     </fieldset>
                    </div>
                    </div>
                 </div>    
          <div class="form-column">
                    <div class="row">
                    <div class="col-md-12">
                     <fieldset>
                      <legend>Setting 02</legend>
                      <div class="rownew">
                      <div class="col-md-8">
                      <div class="row">
                        <div class="form-group">
                            <label class="col-md-3 label-custom"><input type="radio" name="based_on_type1[]" value="1"<?php echo $row1["based_on_type"]=="1" ? " checked" : "" ?>> Based on the lowest price   </label>
                            <div class="col-md-3">
                                <div class="right-icon-text3">
                                 <select class="form-control" name="minus_plus[]">
                                 	<option value="minus"<?php echo $row1["minus_plus"]=="minus" ? " selected" : "" ?>>minus</option>
                                    <option value="zero"<?php echo $row1["minus_plus"]=="zero" ? " selected" : "" ?>>zero</option>
                                    <option value="plus"<?php echo $row1["minus_plus"]=="plus" ? " selected" : "" ?>>plus</option>
                                 </select>
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                               <strong>$</strong>
                            </div>
                            <div class="col-md-3">
                                <div class="right-icon-text3">
                                 <input class="form-control" placeholder="Enter" type="text" name="lowest_price[]" style="width:100px;" value="<?php echo $row1["lowest_price"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="form-group">
                            <label class="col-md-3 label-custom"><input type="radio" name="based_on_type1[]" value="0"<?php echo $row1["based_on_type"]=="0" ? " checked" : "" ?>> Base on Cart price  </label>
                        </div>
                        </div>
                       </div>  
                      </div>
                     </fieldset>
                    </div>
                    </div>
                 </div> 
                 </fieldset>             
                      <p class="setting-terms"><strong>※</strong> Setting 01 & 02 excludes FBA commodities.</p>     
                  <div class="form-group text-center">
                     <button class="btn btn-sm btn-primary">Save</button>
                  </div>       
                          </div>
                          <div id="menu1" class="tab-pane fade in">
                            <div class="form-column">
                                <h3 class="form-head">SHIPPING RATE</h3>
                                <div class="row">
                                <div class="col-md-6">
                                 <fieldset>
                                  <legend>Category 1</legend>
                                     <div class="form-group">
                                        <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="standard_type2[]" value="SAL"<?php echo $row2["standard_type"]=="SAL" ? " checked" : "" ?>>SAL  </label>
                                        <label class="radio-inline"><input type="radio" name="standard_type2[]" value="E Bucket"<?php echo $row2["standard_type"]=="E Bucket" ? " checked" : "" ?>>E Bucket  </label>
                                        <label class="radio-inline"><input type="radio" name="standard_type2[]" value="EMS"<?php echo $row2["standard_type"]=="EMS" ? " checked" : "" ?>>EMS  </label>
                                        <label class="radio-inline"><input type="radio" name="standard_type2[]" value="D mail"<?php echo $row2["standard_type"]=="D mail" ? " checked" : "" ?>>D Mail  </label>
                                        </div>
                                     </div>
                                     <p>Less than 2kg & Less than 80 cm in total & Less than 60 cm at the longest side</p>
                                     <div class="col-md-12">
                                          <div class="row">
                                         <div class="form-group row clearfix">
                                        <label class="col-md-4">Handling comm. per article  </label>
                                        <div class="col-md-8">
                                           <div class="text-symbol">
                                           <span class="currency-icon">¥</span>  <input class="form-control" type="text" name="commission[]" value="<?php echo $row2["commission"]?>">
                                           </div>
                                        </div>
                                        </div>
                                        </div>
                                     </div>
                                 </fieldset>
                                </div>
                                <div class="col-md-6">
                                 <fieldset>
                                  <legend>Category 2</legend>
                                     <div class="form-group">
                                        <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="out_standard_type2[]" value="SAL"<?php echo $row2["out_standard_type"]=="SAL" ? " checked" : "" ?>>SAL  </label>
                                        <label class="radio-inline"><input type="radio" name="out_standard_type2[]" value="EMS"<?php echo $row2["out_standard_type"]=="EMS" ? " checked" : "" ?>>EMS  </label>
                                        </div>
                                     </div>
                                     <p>More than 2kg or More than 80 cm in total & More than 60 cm at the longest side</p>
                                     <div class="col-md-12">
                                          <div class="row">
                                         <div class="form-group row clearfix">
                                        <label class="col-md-4">Handling comm. per article  </label>
                                        <div class="col-md-8">
                                           <div class="text-symbol">
                                           <span class="currency-icon">¥</span>  <input class="form-control" type="text" name="out_commission[]" value="<?php echo $row2["out_commission"]?>">
                                           </div>
                                        </div>
                                        </div>
                                        </div>
                                     </div>
                                 </fieldset>
                                </div>
                                </div>
                                </div>
                                <div class="form-column">
                                <div class="row">
                                <div class="col-md-6">
                                 <fieldset>
                                  <legend>Category 3</legend>
                                     <p>music/DVD/Bluelay.PC game/TV game</p>
                                     <div class="col-md-12">
                                          <div class="row">
                                         <div class="form-group row clearfix custop-item-space">
                                        <label class="col-md-4">Shipping rate per item</label>
                                        <div class="col-md-8">
                                           <div class="text-symbol">
                                           <span class="currency-icon">¥</span>  <input class="form-control" type="text" name="others_shipping_rate[]" value="<?php echo $row2["others_shipping_rate"]?>">
                                           </div>
                                        </div>
                                        </div>
                                        </div>
                                     </div>
                                 </fieldset>
                                </div>
                                <div class="col-md-6">
                                 <fieldset>
                                  <legend>Category 4</legend>
                                     <p>No Weight Discription</p>
                                     <div class="col-md-12">
                                          <div class="row">
                                         <div class="form-group row clearfix custop-item-space">
                                        <label class="col-md-4">Shipping rate per item</label>
                                        <div class="col-md-8">
                                           <div class="text-symbol">
                                           <span class="currency-icon">¥</span>  <input class="form-control" type="text" name="others2_shipping_rate[]" value="<?php echo $row2["others2_shipping_rate"]?>">
                                           </div>
                                        </div>
                                        </div>
                                        </div>
                                     </div>
                                 </fieldset>
                                </div>
                                </div>
                             </div>
                             
               <div class="form-column">
                    <div class="row">
                    <div class="col-md-12">
                     <fieldset>
                      <legend>PACKAGE WEIGHT</legend>
                      <div class="rownew">
                      <div class="col-md-4">
                          <div class="row">
                        <div class="form-group">
                            <label class="col-md-6 label-custom"> COMMODITY WEIGHT <small>LESS THAN 500G</small>    </label>
                            <div class="col-md-6">
                                <div class="right-icon-text">
                                  <span class="right-text-icon">g</span>  <input type="text"  class="form-control" placeholder="Enter" name="weight_less[]" value="<?php echo $row2["weight_less"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                       </div>  
                       <div class="col-md-4">
                          <div class="row">
                        <div class="form-group">
                            <label class="col-md-6 label-custom"> COMMODITY WEIGHT  <small>between 501g and 1001g</small>    </label>
                            <div class="col-md-6">
                                <div class="right-icon-text">
                                  <span class="right-text-icon">g</span>  <input type="text"  class="form-control" placeholder="Enter" name="weight_between[]" value="<?php echo $row2["weight_between"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                       </div>
                       <div class="col-md-4">
                          <div class="row">
                        <div class="form-group">
                            <label class="col-md-6 label-custom"> COMMODITY WEIGHT <small>more than 1001g</small>    </label>
                            <div class="col-md-6">
                                <div class="right-icon-text">
                                  <span class="right-text-icon">g</span>  <input type="text"  class="form-control" placeholder="Enter" name="weight_more[]" value="<?php echo $row2["weight_more"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                       </div>
                      </div>
                   <div class="col-md-7">
                       <div class="section-new-table">
                        <div class="table-responsive">
              <table class="table table-bordered" width="100%">
              <tbody><tr>
                <td width="22%">Condition Note</td>
                <!-- <input type="text" class="form_control" name="note[]" style="width:500px" value="<?php echo $row2["note"]?>"/> -->
                <td colspan="3" align="left" style="text-align:left;" class="color-light">
                  <textarea name="note[]" rows="2" cols="60"><?php echo $row2["note"]!="" ? $row2["note"] : 'Brand new.The commodity will be shipped by Japan Speed Post EMS.Purchased commodity will arrive at your home in approx 5 days.'; ?></textarea></td>
              </tr>
              <tr>
                <td>Handling Time</td>
                <td width="25%">
                <select class="form-control" name="handling_time[]">
                  <option value="3"<?php echo $row2["handling_time"]=="3" ? " selected" : "" ?>>3 days</option>
                    <option value="5"<?php echo $row2["handling_time"]=="5" ? " selected" : "" ?>>5 days</option>
                    <option value="10"<?php echo $row2["handling_time"]=="10" ? " selected" : "" ?>>10 days</option>
                </select>
                </td>
                <td style="text-align:center" width="18%">出品個数</td>
                <td width="25%">
                <select class="form-control" name="num_of_articles[]">
                  <option value="1"<?php echo $row2["num_of_articles"]=="1" ? " selected" : "" ?>>1</option>
                    <option value="2"<?php echo $row2["num_of_articles"]=="2" ? " selected" : "" ?>>2</option>
                    <option value="3"<?php echo $row2["num_of_articles"]=="3" ? " selected" : "" ?>>3</option>
                    <option value="4"<?php echo $row2["num_of_articles"]=="4" ? " selected" : "" ?>>4</option>
                    <option value="5"<?php echo $row2["num_of_articles"]=="5" ? " selected" : "" ?>>5</option>
                    <option value="6"<?php echo $row2["num_of_articles"]=="6" ? " selected" : "" ?>>6</option>
                    <option value="7"<?php echo $row2["num_of_articles"]=="7" ? " selected" : "" ?>>7</option>
                    <option value="8"<?php echo $row2["num_of_articles"]=="8" ? " selected" : "" ?>>8</option>
                    <option value="9"<?php echo $row2["num_of_articles"]=="9" ? " selected" : "" ?>>9</option>
                    <option value="10"<?php echo $row2["num_of_articles"]=="10" ? " selected" : "" ?>>10</option>
                </select>
                </td>
              </tr>
            </tbody>
            </table>
              </div>
                       </div>
                   </div>  
                     </fieldset>
                    </div>
                    </div>
                 </div>
                <div class="form-column">
                    <div class="row">
                    <div class="col-md-12">
                     <fieldset>
                      <legend>Setting 01</legend>
                      <div class="rownew">
                      <div class="col-md-2">
                         <p>Selling Price Adjustment</p>
                      </div>
                      <div class="col-md-8">
                      <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 label-custom"><input type="radio" name="profit_type2[]" value="amount"<?php echo $row2["profit_type"]=="amount" ? " checked" : "" ?>> profit amount    </label>
                            <div class="col-md-2">
                                <div class="right-icon-text2">
                                  <span class="right-text-icon">Yen</span>  <input class="form-control" placeholder="Enter" type="text" name="profit_amount_from[]" style="width:90px;" value="<?php echo $row2["profit_amount_from"]?>">
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                               <strong>~</strong>
                            </div>
                            <div class="col-md-2">
                                <div class="right-icon-text2">
                                  <span class="right-text-icon">Yen</span>  <input class="form-control" placeholder="Enter" type="text" name="profit_amount_to[]" style="width:90px;" value="<?php echo $row2["profit_amount_to"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="form-group">
                            <label class="col-md-2 label-custom"><input type="radio" name="profit_type2[]" value="rate"<?php echo $row2["profit_type"]=="rate" ? " checked" : "" ?>> profit rate    </label>
                            <div class="col-md-2">
                                <div class="right-icon-text2">
                                  <span class="right-text-icon">%</span>  <input class="form-control" placeholder="Enter" type="text" name="profit_rate_from[]" style="width:90px;" value="<?php echo $row2["profit_rate_from"]?>">
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                               <strong>~</strong>
                            </div>
                            <div class="col-md-2">
                                <div class="right-icon-text2">
                                  <span class="right-text-icon">%</span>  <input class="form-control" placeholder="Enter" type="text" name="profit_rate_to[]" style="width:90px;" value="<?php echo $row2["profit_rate_to"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                       </div>  
                      </div>
                     </fieldset>
                    </div>
                    </div>
                 </div>    
          <div class="form-column">
                    <div class="row">
                    <div class="col-md-12">
                     <fieldset>
                      <legend>Setting 02</legend>
                      <div class="rownew">
                      <div class="col-md-8">
                      <div class="row">
                        <div class="form-group">
                            <label class="col-md-3 label-custom"><input type="radio" name="based_on_type2[]" value="1"<?php echo $row2["based_on_type"]=="1" ? " checked" : "" ?>> Based on the lowest price   </label>
                            <div class="col-md-3">
                                <div class="right-icon-text3">
                                 <select class="form-control" name="minus_plus[]">
                                  <option value="minus"<?php echo $row2["minus_plus"]=="minus" ? " selected" : "" ?>>minus</option>
                                    <option value="zero"<?php echo $row2["minus_plus"]=="zero" ? " selected" : "" ?>>zero</option>
                                    <option value="plus"<?php echo $row2["minus_plus"]=="plus" ? " selected" : "" ?>>plus</option>
                                 </select>
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                               <strong>$</strong>
                            </div>
                            <div class="col-md-3">
                                <div class="right-icon-text3">
                                 <input class="form-control" placeholder="Enter" type="text" name="lowest_price[]" style="width:100px;" value="<?php echo $row2["lowest_price"]?>">
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="form-group">
                            <label class="col-md-3 label-custom"><input type="radio" name="based_on_type2[]" value="0"<?php echo $row2["based_on_type"]=="0" ? " checked" : "" ?>> Base on Cart price  </label>
                        </div>
                        </div>
                       </div>  
                      </div>
                     </fieldset>
                    </div>
                    </div>
                 </div>              
                      <p class="setting-terms"><strong>※</strong> Setting 01 & 02 excludes FBA commodities.</p>     
                  <div class="form-group text-center">
                     <button class="btn btn-sm btn-primary">Save</button>
                  </div>        
                          </div>
                        </div>
                        </div>
                   </div>
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
