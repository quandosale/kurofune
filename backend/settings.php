<?php
	require_once("db.php");
	//	
	$seller_id = get_param("seller_id");
	$access_key = get_param("access_key");
	$secret_key = get_param("secret_key");
	$standard_type1 = get_param("standard_type1");
	if($standard_type1==null)	$standard_type1[0] = "SAL";
	$standard_type2 = get_param("standard_type2");
	if($standard_type2==null)	$standard_type2[0] = "SAL";
	$commission = get_param("commission");
	$out_standard_type1 = get_param("out_standard_type1");
	if($out_standard_type1==null)	$out_standard_type1[0] = "SAL";
	$out_standard_type2 = get_param("out_standard_type2");
	if($out_standard_type2==null)	$out_standard_type2[0] = "SAL";
	$out_commission = get_param("out_commission");
	$others_shipping_rate = get_param("others_shipping_rate");
	$others2_shipping_rate = get_param("others2_shipping_rate");
	$others_commission = get_param("others_commission");
	$weight_less = get_param("weight_less");
	$weight_between = get_param("weight_between");
	$weight_more = get_param("weight_more");
	$note = get_param("note");
	$handling_time = get_param("handling_time");
	$num_of_articles = get_param("num_of_articles");
	$profit_type1 = get_param("profit_type1");
	if($profit_type1==null)	$profit_type1[0] = "amount";
	$profit_type2 = get_param("profit_type2");
	if($profit_type2==null)	$profit_type2[0] = "amount";
	$profit_amount_from = get_param("profit_amount_from");
	$profit_amount_to = get_param("profit_amount_to");
	$profit_rate_from = get_param("profit_rate_from");
	$profit_rate_to = get_param("profit_rate_to");
	$based_on_type1 = get_param("based_on_type1");
	if($based_on_type1==null)	$based_on_type1[0] = "1";
	$based_on_type2 = get_param("based_on_type2");
	if($based_on_type2==null)	$based_on_type2[0] = "1";
	$minus_plus = get_param("minus_plus");
	$lowest_price = get_param("lowest_price");
	$cart_price = get_param("cart_price");
	//
	$cmd = get_param("cmd");
	if($cmd=="SAVE"){
		$link = connect();
		// US
		$i=0;
		$values = ["country_id" => ($i+1)
					,"seller_id"=>strtoupper($seller_id[$i])
					,"access_key"=>strtoupper($access_key[$i])
					,"secret_key"=>$secret_key[$i]
					,"standard_type"=>$standard_type1[$i]
					,"commission"=>$commission[$i]
					,"out_standard_type"=>$out_standard_type1[$i]
					,"out_commission"=>$out_commission[$i]
					,"others_shipping_rate"=>$others_shipping_rate[$i]
					,"others2_shipping_rate"=>$others2_shipping_rate[$i]
					,"others_commission"=>$others_commission[$i]
					,"weight_less"=>$weight_less[$i]
					,"weight_between"=>$weight_between[$i]
					,"weight_more"=>$weight_more[$i]
					,"note"=>$note[$i]
					,"handling_time"=>$handling_time[$i]
					,"num_of_articles"=>$num_of_articles[$i]
					,"profit_type"=>$profit_type1[$i]
					,"profit_amount_from"=>$profit_amount_from[$i]
					,"profit_amount_to"=>$profit_amount_to[$i]
					,"profit_rate_from"=>$profit_rate_from[$i]
					,"profit_rate_to"=>$profit_rate_to[$i]
					,"based_on_type"=>$based_on_type1[$i]
					,"minus_plus"=>$minus_plus[$i]
					,"lowest_price"=>$lowest_price[$i]
					,"cart_price"=>$cart_price[$i]
					,"user_id"=>$_SESSION['user_id']];
		replace(TABLE_SETTINGS,$values,$link);
		// CA
		$i=1;
		$values = ["country_id" => ($i+1)
					,"seller_id"=>strtoupper($seller_id[$i])
					,"access_key"=>strtoupper($access_key[$i])
					,"secret_key"=>$secret_key[$i]
					,"standard_type"=>$standard_type2[0]
					,"commission"=>$commission[$i]
					,"out_standard_type"=>$out_standard_type2[0]
					,"out_commission"=>$out_commission[$i]
					,"others_shipping_rate"=>$others_shipping_rate[$i]
					,"others2_shipping_rate"=>$others2_shipping_rate[$i]
					,"others_commission"=>$others_commission[$i]
					,"weight_less"=>$weight_less[$i]
					,"weight_between"=>$weight_between[$i]
					,"weight_more"=>$weight_more[$i]
					,"note"=>$note[$i]
					,"handling_time"=>$handling_time[$i]
					,"num_of_articles"=>$num_of_articles[$i]
					,"profit_type"=>$profit_type2[0]
					,"profit_amount_from"=>$profit_amount_from[$i]
					,"profit_amount_to"=>$profit_amount_to[$i]
					,"profit_rate_from"=>$profit_rate_from[$i]
					,"profit_rate_to"=>$profit_rate_to[$i]
					,"based_on_type"=>$based_on_type2[0]
					,"minus_plus"=>$minus_plus[$i]
					,"lowest_price"=>$lowest_price[$i]
					,"cart_price"=>$cart_price[$i]
					,"user_id"=>$_SESSION['user_id']];
		replace(TABLE_SETTINGS,$values,$link);
	}
?>