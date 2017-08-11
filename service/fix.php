<?php
	require_once(dirname(dirname(__FILE__))."/backend/db.php");
	require_once(dirname(dirname(__FILE__))."/backend/Client.php");
	//
	$link = connect();
	$user_settings = get_shipping_rates();
	//
	$mode = isset($argv) && count($argv) > 1 ? $argv[1] : "";
	$from = isset($argv) && count($argv) > 2 ? $argv[2] : "";
	$limit = isset($argv) && count($argv) > 3 ? $argv[3] : "";
	$res = null;
	if($mode==""|| $from==""||$limit=="")
		exit;
	//
	$limit_sql = " LIMIT $from,$limit";
	if($mode=="ship")
    	//$res = query(TABLE_PRODUCTS,[]," WHERE shipping%5<>0 or (shipping=1500 and weight<>'') ORDER BY updated DESC",$link);
    	//$res = query(TABLE_PRODUCTS,[]," WHERE ((lower(category) like '%blu%' and lower(category) like '%ray%') OR (lower(category) like '%pc%' and lower(category) like '%game%') OR (lower(category) like '%video%' and lower(category) like '%game%') OR (lower(category) like '%CD%') OR (lower(category) like '%dvd%') OR (lower(category) like '%music%')) and (weight<>'')",$link);
    	$res = query(TABLE_PRODUCTS,[],"",$link);
    else if($mode=="image_jp")
    	$res = query(TABLE_PRODUCTS,[]," WHERE image_jp='' ORDER BY updated DESC",$link);
    else if($mode=="commodity")
    	$res = query(TABLE_PRODUCTS,[]," WHERE commodity='' and price_jp>0 ORDER BY updated DESC",$link);
    else if($mode=="price_us")
    	// $res = query(TABLE_PRODUCTS,[]," WHERE price_us=0 and num_of_sellers<>0 ORDER BY updated DESC".$limit_sql,$link);
    	$res = query(TABLE_PRODUCTS,[]," WHERE price_us>50 ORDER BY updated DESC",$link);
    else if($mode=="price_jp")
    	// $res = query(TABLE_PRODUCTS,[]," WHERE price_jp_prime=0 ORDER BY updated DESC".$limit_sql,$link);
    	// $res = query(TABLE_PRODUCTS,[]," WHERE price_jp=0 ORDER BY updated DESC".$limit_sql,$link);
    	$res = query(TABLE_PRODUCTS,[]," WHERE price_jp=0 ORDER BY updated DESC".$limit_sql,$link);
    else if($mode=="weight")
    	// $res = query(TABLE_PRODUCTS,[]," WHERE weight='' and price_jp>0 and category not like '%CDs%' ORDER BY updated DESC".$limit_sql,$link);
    	$res = query(TABLE_PRODUCTS,[],"".$limit_sql,$link);
    else if($mode=="dim")
    	$res = query(TABLE_PRODUCTS,[]," WHERE dimensions='' and price_jp>0 and category not like '%CDs%' ORDER BY updated DESC".$limit_sql,$link);
    else if($mode=="cat")
    	// $res = query(TABLE_PRODUCTS,[]," WHERE category='' and image_us<>'' ORDER BY updated DESC".$limit_sql,$link);
    	$res = query(TABLE_PRODUCTS,[],"".$limit_sql,$link);
    else{
    	echo "Wrong Arguments!";
    	exit;
    }
    //
    while($row = mysqli_fetch_array($res)){
    	$asin = $row['asin'];
		$result = [];
		if($mode=="ship"){
			// Shipping Rate
			$us_shipping = get_shipping_table_value($user_settings,$row['weight'],$row['dimensions'],$row['category']);
			$result['shipping'] = $us_shipping;
		}
		else if($mode=="image_jp"){
			$amazon_jp_url = "https://www.amazon.co.jp/gp/product/jp-common-black-curtain-redirect.html?ie=UTF8&blackCurtainPurpose=CERO_ITEM&redirectUrl=%2Fgp%2Fproduct%2F$asin";
			$html_jp = "";
			$html_jp = get_html($amazon_jp_url);
			preg_match('/,\"large\":\"([^"]+)\",/si',$html_jp,$matches_jp);
			if(!isset($matches_jp) || count($matches_jp) == 0)
				preg_match('/id=\"imgBlkFront\" data-a-dynamic-image=\"\{&quot;(.*?)&quot;/si',$html_jp,$matches_jp);
			$jp_image = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
			if($jp_image==""){
				preg_match('/id=\"landingImage\" data-a-dynamic-image=\"\{&quot;(.*?)&quot;/si',$html_jp,$matches_jp);
				$jp_image = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
			}
			if($jp_image!="")
				$result['image_jp'] = $jp_image;
		}
		else if($mode=="commodity"){
			$amazon_jp_url = "https://www.amazon.co.jp/gp/product/jp-common-black-curtain-redirect.html?ie=UTF8&blackCurtainPurpose=CERO_ITEM&redirectUrl=%2Fgp%2Fproduct%2F$asin";
			$html_jp = "";
			$html_jp = get_html($amazon_jp_url);
			// Commodity
			preg_match('/\<div id=\"imgTagWrapperId\" class=\"imgTagWrapper\"\>[^<]+\<img alt=\"([^"]+)\"/si', $html_jp, $matches_jp);
			if(!isset($matches_jp) || count($matches_jp) == 0){
				preg_match('/\<meta name=\"keywords\" content=\"([^\",]+)[\",]/si', $html_jp, $matches_jp);
				if(!isset($matches_jp) || count($matches_jp) == 0)
					preg_match('/\<span id=\"productTitle\"[^>]*\>([^<]+)\<\/span\>/si', $html_jp, $matches_jp);
			}
			$commodity = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
			if($commodity!="")
				$result['commodity'] = clean_str($commodity);
		}
		else if($mode=="price_us"){
			// 3 Prices
			$amazon_us_url = "https://www.amazon.com/dp/$asin";
			$html_us = "";
			$html_us = get_html($amazon_us_url);
			preg_match('/href=\"\/gp\/offer-listing\/([^\/]+)\/[^"]+\"/si', $html_us, $matches_us);
			$asin_in_url = isset($matches_us[1]) ? $matches_us[1] : $asin;
			$prices_3 = [];
			$prices_url = "https://www.amazon.com/gp/offer-listing/$asin_in_url/ref=dp_olp_new?ie=UTF8&condition=new";
			$prices_html = get_html($prices_url);
			preg_match_all('/\<span class=\"[^"]+olpOfferPrice[^"]+\"\>(.*?)\<\/span\>(.*?)\<p class=\"olpShippingInfo\"\>(.*?)\<\/p\>/si', $prices_html, $prices_matches);
			$i=0;
			$price_us = 0;
			$price_us_prime = 0;
			foreach ($prices_matches[1] as $price_part) {
				$prime_part = $prices_matches[2][$i];
				$shipping_part = $prices_matches[3][$i++];
				preg_match('/\$([0-9\.,]+)/si', $price_part, $price_matches);
				$price = get_value($price_matches[1],0);
				$prime = stripos($prime_part, "Amazon Prime")!==false ? 1 : 0;
				preg_match('/\$([0-9\.,]+)/si', $shipping_part, $shipping_matches);
				$shipping = stripos($shipping_part,"FREE") ? 0 : get_value($shipping_matches[1],0);
				if($i<=3)
					$prices_3[] = "$price:$prime:$shipping";
				if($price_us_prime==0 && $prime==1){
					$price_us_prime = $price+$shipping;
				}
				if($price_us==0){
					$price_us = $price + $shipping;
				}
				if($i>2 && $price_us_prime!=0)
					break;
			}
			if($price_us_prime==0)
				$price_us_prime = $price_us;
			if($price_us){
				$result['price_us'] = $price_us;
				$result['price_us_prime'] = $price_us_prime;
				$result['prices_3_us'] = implode(";", $prices_3);
			}
		}
		else if($mode=="price_jp"){
			// Prices 3 JP
			$amazon_jp_url = "https://www.amazon.co.jp/gp/product/jp-common-black-curtain-redirect.html?ie=UTF8&blackCurtainPurpose=CERO_ITEM&redirectUrl=%2Fgp%2Fproduct%2F$asin";
			$html_jp = "";
			$html_jp = get_html($amazon_jp_url);
			preg_match('/href=\"\/gp\/offer-listing\/([^\/]+)\/[^"]+\"/si', $html_jp, $matches_jp);
			if(isset($matches_jp) && count($matches_jp)>0){
				$asin_in_url = isset($matches_jp[1]) ? $matches_jp[1] : $asin;
				$prices_3 = [];
				$prices_url = "https://www.amazon.co.jp/gp/offer-listing/$asin_in_url/ref=dp_olp_new?ie=UTF8&condition=new";
				$prices_html = get_html($prices_url,false);
				preg_match_all('/\<span class=\"[^"]+olpOfferPrice[^"]+\"\>(.*?)\<\/span\>(.*?)\<p class=\"olpShippingInfo\"\>(.*?)\<\/p\>/si', $prices_html, $prices_matches);
				preg_match_all('/\<div[^>]+class=\"[^"]*olpDeliveryColumn[^"]*\"[^>]+\>(.*?)\<\/div\>/si', $prices_html, $prices_matches2);
				$i=0;
				$price_jp = 0;
				$price_jp_prime = 0;
				$expected_arrival_ = "";
				$expected_arrival_prime = "";
				foreach ($prices_matches[1] as $price_part) {
					$prime_part = $prices_matches[2][$i];
					$shipping_part = $prices_matches[3][$i];
					$arrival_part = $prices_matches2[1][$i++];
					preg_match('/￥ ([0-9\.,]+)/si', $price_part, $price_matches);
					$price = get_value(str_replace(",","",$price_matches[1]),0);
					$prime = stripos($prime_part, "Amazon Prime")!==false ? 1 : 0;
					preg_match('/￥ ([0-9\.,]+)/si', $shipping_part, $shipping_matches);
					$shipping = stripos($shipping_part,"通常配送無料") ? 0 : get_value($shipping_matches[1],0);
					preg_match('/([0-9\/]+[^><]*[0-9\/]+[^><]*〜[^><]*[0-9\/]+)/si',$arrival_part, $arrival_matches);
					$expected_arrival = get_value(clean_str($arrival_matches[1]));
					if($i<=3)
						$prices_3[] = "$price:$prime:$shipping";
					if($price_jp_prime==0 && $prime==1){
						$price_jp_prime = $price+$shipping;
						$expected_arrival_prime = "PRIME";
					}
					if($price_jp==0){
						$price_jp = $price + $shipping;
						$expected_arrival_ = ($prime==1 ? "PRIME" : $expected_arrival);
					}
					if($i>2 && $price_jp_prime!=0)
						break;
				}
				if($price_jp_prime==0)
					$price_jp_prime = $price_jp;
				$result['price_jp'] = $price_jp;
				$result['price_jp_prime'] = $price_jp_prime;
				$result['prices_3_jp'] = implode(";", $prices_3);
			}
			else{
				$price_jp = 0;
				preg_match('/\<span id=\"priceblock_ourprice\" class=\"a-size-medium a-color-price\">(.*?)￥ ([0-9\.,]+)(.*?)\<\/span\>/si',$html_jp,$matches_jp);
				if(!isset($matches_jp) || count($matches_jp) == 0){
					preg_match('/condition=([^"]+)\"\>[^<]+\<\/a\>\<span class=["\']a-color-price["\']>.*?￥ ([0-9\.,]+).*?\<\/span\>/si',$html_jp,$matches_jp);
					if(!isset($matches_jp) || count($matches_jp) == 0){
						preg_match('/\<span class=\"inlineBlock-display\"\>.*?￥ ([0-9\.,]+)(.*?)\<\/div\>/si',$html_jp,$matches_jp);
						if(isset($matches_jp) && count($matches_jp) > 0){
							$price1 = str_replace(",","",$matches_jp[1]);
							preg_match('/￥ ([0-9\.,]+)/si',$matches_jp[2],$matches_jp2);
							$price2 = isset($matches_jp2) && count($matches_jp2) > 0 ? str_replace(",","",$matches_jp2[1]) : 0;
							$price_jp = $price1 + $price2;
							echo "$price1---$price2---$price_jp\n";
						}
					}
					else
						$price_jp = isset($matches_jp) && count($matches_jp) > 0 && $matches_jp[1]!="used" ? str_replace(",","",$matches_jp[2]) : "0.00";
				}
				else
					$price_jp = isset($matches_jp) && count($matches_jp) > 0 ? str_replace(",","",$matches_jp[2]) : "0.00";
				// JP Shipping
				preg_match('/\<span class=\"[^"]*shipping3P[^"]*\"\>[^<]*￥ ([0-9\.,]+)[^<]*\<\/span\>/si', $html_jp, $matches_jp);
				$shipping_jp = isset($matches_jp) && count($matches_jp) > 0 ? str_replace(",","",$matches_jp[1]) : 0;
				$result['price_jp'] = $price_jp + $shipping_jp;
				$result['price_jp_prime'] = $price_jp;
			}
		}
		else if($mode=="weight"){
			$amazon_jp_url = "https://www.amazon.co.jp/gp/product/jp-common-black-curtain-redirect.html?ie=UTF8&blackCurtainPurpose=CERO_ITEM&redirectUrl=%2Fgp%2Fproduct%2F$asin";
			$html_jp = "";
			$html_jp = get_html($amazon_jp_url);
			preg_match('/\<tr[^>]+\>\<td[^>]+\>商品重量\<\/td\>\<td[^>]+\>(.*?)\<\/td\>\<\/tr\>/si',$html_jp,$matches_jp);
			$us_weight = isset($matches_jp) && count($matches_jp) > 0 ? clean_str($matches_jp[1]) : "";
			if($us_weight==''){
				preg_match('/\<tr[^>]+\>\<td[^>]+\>発送重量\<\/td\>\<td[^>]+\>(.*?)\<\/td\>\<\/tr\>/si',$html_jp,$matches_jp);
				$us_weight = isset($matches_jp) && count($matches_jp) > 0 ? clean_str($matches_jp[1]) : "";
				if($us_weight==""){
					preg_match('/\<li\>[^<]*\<b\>[^<]*商品重量:[^<]*\<\/b\>(.*?)\<\/li\>/si',$html_jp,$matches_jp);
					$us_weight = isset($matches_jp) && count($matches_jp) > 0 ? clean_str($matches_jp[1]) : "";
					if($us_weight==""){
						preg_match('/\<li\>[^<]*\<b\>[^<]*発送重量:[^<]*\<\/b\>(.*?)\<\/li\>/si',$html_jp,$matches_jp);
						$us_weight = isset($matches_jp) && count($matches_jp) > 0 ? clean_str($matches_jp[1]) : "";
						if($us_weight==""){
							preg_match('/\<li\>[^<]*\<b\>[^<]*商品パッケージの寸法:[^<]*\<\/b\>[^;]+;(.*?)\<\/li\>/si',$html_jp,$matches_jp);
							$us_weight = isset($matches_jp) && count($matches_jp) > 0 ? clean_str($matches_jp[1]) : "";
						}
					}
				}
				if($us_weight=='')
					continue;
			}
			$result['weight'] = $us_weight;
		}
		else if($mode=="dim"){
			// Dimensions
			$amazon_jp_url = "https://www.amazon.co.jp/gp/product/jp-common-black-curtain-redirect.html?ie=UTF8&blackCurtainPurpose=CERO_ITEM&redirectUrl=%2Fgp%2Fproduct%2F$asin";
			$html_jp = "";
			$html_jp = get_html($amazon_jp_url);
			$us_dimensions = '';
			preg_match('/\<tr[^>]+\>\<td[^>]+\>梱包サイズ\<\/td\>\<td[^>]+\>(.*?)\<\/td\>\<\/tr\>/si',$html_jp,$matches_jp);
			if(isset($matches_jp) && count($matches_jp) > 0){
				$us_dimensions = isset($matches_jp) && count($matches_jp) > 0 ? clean_str($matches_jp[1]) : "";
				$result['dimensions'] = $us_dimensions;
			}
			else
				continue;
		}
		else if($mode=="cat"){
			$amazon_jp_url = "https://www.amazon.co.jp/gp/product/jp-common-black-curtain-redirect.html?ie=UTF8&blackCurtainPurpose=CERO_ITEM&redirectUrl=%2Fgp%2Fproduct%2F$asin";
			$html_jp = "";
			$html_jp = get_html($amazon_jp_url);
			preg_match('/\<div id=\"wayfinding-breadcrumbs_feature_div\"[^>]+\>(.*?)\<\/div\>/si',$html_jp,$matches_jp);
			$category = isset($matches_jp) && count($matches_jp) > 0 ? clean_str(str_replace("rsaquo;",",",$matches_jp[1])) : "";
			if($category==""){	
				preg_match('/\<option current=\'parent\' selected=\'selected\' value=\'search-alias=[^\']+\'\>([^<]+\<\/option\>/si',$html_jp,$matches_jp);
				$category = isset($matches_jp) && count($matches_jp) > 0 ? clean_str(str_replace("rsaquo;",",",$matches_jp[1])) : "";
			}
			if($category!="")
				$result['category'] = $category;
		}
		echo "$asin\n";
		echo "<PRE>";
		print_r($result);
		echo "</PRE>";
		// exit;
		if(count($result)>0)
			update(TABLE_PRODUCTS,$result," WHERE asin='$asin'",$link);
    }
?>
