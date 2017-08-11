<?php
	require_once(dirname(dirname(__FILE__))."/backend/db.php");
	require_once(dirname(dirname(__FILE__))."/backend/Client.php");
	// 3: New Extraction 	1:Selling
	$mode = isset($argv) && count($argv) > 1 ? $argv[1] : "";
	$from = isset($argv) && count($argv) > 2 ? $argv[2] : "";
	$limit = isset($argv) && count($argv) > 3 ? $argv[3] : "";
	if($mode==""||($mode==STATUS_NEW && ($from==""||$limit=="")))
		exit;
	//
	$user_settings_all = [];
	$link = connect();
	$limit_sql = $mode==STATUS_NEW ? " LIMIT $from,$limit" : "";
    $res = query(TABLE_PRODUCTS,[]," WHERE status=".$mode." ORDER BY updated DESC".$limit_sql,$link);
    $sell_asins = [];
    while($row = mysqli_fetch_array($res)){
    	if(!array_key_exists($row['user_id'], $user_settings_all))
    		$user_settings_all[$row['user_id']] = get_shipping_rates($row['user_id']);
    	$user_settings = $user_settings_all[$row['user_id']];
    	$asin = $row['asin'];
    	/*$config = array (
		  'ServiceURL' => "https://mws.amazonservices.com"
		);
		$service = new MarketplaceWebService_Client(
					     AWS_ACCESS_KEY_ID, 
					     AWS_SECRET_ACCESS_KEY, 
					     $config,
					     APPLICATION_NAME,
					     APPLICATION_VERSION);*/
		$result = [];
		$amazon_us_url = "https://www.amazon.com/dp/$asin";
		$amazon_jp_url = "https://www.amazon.co.jp/gp/product/jp-common-black-curtain-redirect.html?ie=UTF8&blackCurtainPurpose=CERO_ITEM&redirectUrl=%2Fgp%2Fproduct%2F$asin";
		$html_us = "";
		$html_us = get_html($amazon_us_url);
		file_put_contents("sample_us.html", $html_us);
		$html_jp = get_html($amazon_jp_url);
		file_put_contents("sample_jp.html", $html_jp);
		// DVD, ブルーレイ, ミュージック, TVゲーム, PCゲーム
		preg_match('/\<div id=\"wayfinding-breadcrumbs_feature_div\"[^>]+\>(.*?)\<\/div\>/si',$html_jp,$matches_jp);
		$category = isset($matches_jp) && count($matches_jp) > 0 ? clean_str(str_replace("rsaquo;",",",$matches_jp[1])) : "";
		if($category==""){	
			preg_match('/\<option current=\'parent\' selected=\'selected\' value=\'search-alias=[^\']+\'\>([^<]+)\<\/option\>/si',$html_jp,$matches_jp);
			$category = isset($matches_jp) && count($matches_jp) > 0 ? clean_str(str_replace("rsaquo;",",",$matches_jp[1])) : "";
		}
		$result['category'] = $category;
		preg_match('/,\"large\":\"([^"]+)\",/si',$html_jp,$matches_jp);
		if(!isset($matches_jp) || count($matches_jp) == 0)
			preg_match('/id=\"imgBlkFront\" data-a-dynamic-image=\"\{&quot;(.*?)&quot;/si',$html_jp,$matches_jp);
		$jp_image = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
		if($jp_image==""){
			preg_match('/id=\"landingImage\" data-a-dynamic-image=\"\{&quot;(.*?)&quot;/si',$html_jp,$matches_jp);
			$jp_image = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
		}
		$result['image_jp'] = $jp_image;
		preg_match('/,\"large\":\"([^"]+)\",/si',$html_us,$matches_us);
		if(!isset($matches_us) || count($matches_us) == 0)
			preg_match('/id=\"imgBlkFront\" data-a-dynamic-image=\"\{&quot;(.*?)&quot;/si',$html_us,$matches_us);
		$us_image = isset($matches_us) && count($matches_us) > 0 ? $matches_us[1] : "";
		if($us_image==""){
			preg_match('/id=\"landingImage\" data-a-dynamic-image=\"\{&quot;(.*?)&quot;/si',$html_us,$matches_us);
			$us_image = isset($matches_us) && count($matches_us) > 0 ? $matches_us[1] : "";
		}
		$result['image_us'] = $us_image;
		$result['asin'] = $asin;
		$result['user_id'] = $row['user_id'];
		// Commodity
		preg_match('/\<div id=\"imgTagWrapperId\" class=\"imgTagWrapper\"\>[^<]+\<img alt=\"([^"]+)\"/si', $html_jp, $matches_jp);
		if(!isset($matches_jp) || count($matches_jp) == 0){
			preg_match('/\<meta name=\"keywords\" content=\"([^\",]+)[\",]/si', $html_jp, $matches_jp);
			if(!isset($matches_jp) || count($matches_jp) == 0)
				preg_match('/\<span id=\"productTitle\"[^>]*\>([^<]+)\<\/span\>/si', $html_jp, $matches_jp);
		}
		$commodity = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
		$result['commodity'] = clean_str($commodity);
		// Expected Arrival
		/*if($commodity!=""){
			$arrival_url = "https://www.amazon.co.jp/gp/offer-listing/$asin/ref=dp_olp_new?ie=UTF8&condition=new";
			$arrival_html = get_html($arrival_url);
			$expected_arrival = "";
			if(strpos($arrival_html,'わせ買い対象')!==false)
				$expected_arrival = "合わせ買い対象";
			else if(strpos($arrival_html,'aria-label="Amazon Prime (TM)"')!==false)
				$expected_arrival = "PRIME";
			else{
				preg_match('/([0-9\/]+〜[0-9\/]+)/si',$arrival_html, $arrival_matches);
				$expected_arrival = isset($arrival_matches) && count($arrival_matches) > 0 ? $arrival_matches[1] : "";
			}
			$result["days_to_ship"] = $expected_arrival;
		}*/
		//
		/*$xml_res = $service->request(["AWSAccessKeyId"=>AWS_ACCESS_KEY_ID
									,"Action"=>"GetMatchingProduct"
									,"SellerId"=>SELLER_ID
									,"MarketplaceId"=>MARKETPLACE_ID
									,"SignatureMethod"=>"HmacSHA256"
									,"SignatureVersion"=>"2"
									,"Timestamp"=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time())
									,"Version"=>"2011-10-01"
									,"ASINList.ASIN.1"=>"$asin"],AWS_SECRET_ACCESS_KEY,"/Products/2011-10-01");
		$ranks = $xml_res->GetMatchingProductResult->Product->SalesRankings->SalesRank;*/
		/*preg_match('/\<th[^>]+\>[^<]+Best Sellers Rank[^<]+\<\/th\>[^<]+\<td[^>]+\>(.*?)\<\/td\>/si',$html_us,$matches_us);
		if(!isset($matches_us) || count($matches_us) == 0)
			preg_match('/\<li\>[^<]*\<b\>[^<]*Best Sellers Rank:[^<]*\<\/b\>(.*?)\<\/li\>/si',$html_us,$matches_us);
		$rank_html = isset($matches_us) && count($matches_us) > 0 ? $matches_us[1] : "";
		preg_match('/#([0-9,]+)/si', $rank_html, $rank_matches);*/
		preg_match('/\>#([0-9,]+)/si',$html_us,$matches_us);
		$result['ranking'] = isset($matches_us) && count($matches_us) > 0 ? str_replace(",","",$matches_us[1]) : "";
		//
		/*preg_match('/\<span id=\"priceblock_ourprice\" class=\"a-size-medium a-color-price\">.*?\$([0-9\.]+).*?\<\/span\>/si',$html_us,$matches_us);
		if(!isset($matches_us) || count($matches_us) == 0)
			preg_match('/\{\"asin\":\"'.$asin.'\",\"isPreorder\":[0-9]+,\"price\":([0-9\.]+),/si',$html_us,$matches_us);
		$us_price = isset($matches_us) && count($matches_us) > 0 ? $matches_us[1] : "0.00";
		$result['price_us'] = $us_price;*/
		//preg_match('/\<span id=\"ourprice_shippingmessage\"\>(.*?)\<\/span\>/si',$html_us,$matches_us);
		//$us_shipping = isset($matches_us) && count($matches_us) > 0 ? clean_str($matches_us[1]) : "";
		//$us_shipping = doubleval($us_shipping) > 0 ? $us_shipping : "0.00";
		//$result['shipping'] = $us_shipping;
		//
		$us_weight = '';
		preg_match('/\<tr[^>]+\>\<td[^>]+\>商品重量\<\/td\>\<td[^>]+\>(.*?)\<\/td\>\<\/tr\>/si',$html_jp,$matches_jp);
		if(isset($matches_jp) && count($matches_jp) > 0)
			$us_weight = isset($matches_jp) && count($matches_jp) > 0 ? clean_str($matches_jp[1]) : "";
		else{
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
						if($us_weight==""){
							preg_match('/\<th[^>]+\>[^<]+Shipping Weight[^<]+\<\/th\>[^<]+\<td[^>]+\>([^<]+)\<\/td\>/si',$html_us,$matches_us);
							if(!isset($matches_us) || count($matches_us) == 0)
								preg_match('/\<li\>[^<]*\<b\>[^<]*Shipping Weight:[^<]*\<\/b\>(.*?)\<\/li\>/si',$html_us,$matches_us);
							if(!isset($matches_us) || count($matches_us) == 0)
								preg_match('/\<li\>[^<]*\<b\>[^<]*Item Weight:[^<]*\<\/b\>(.*?)\<\/li\>/si',$html_us,$matches_us);
							if(!isset($matches_us) || count($matches_us) == 0)
								preg_match('/\<th[^>]+\>[^<]+Item Weight[^<]+\<\/th\>[^<]+\<td[^>]+\>([^<]+)\<\/td\>/si',$html_us,$matches_us);	
							$us_weight = $us_weight=="" && isset($matches_us) && count($matches_us) > 0 ? clean_str($matches_us[1]) : "";
						}
					}
				}
			}
		}
		$result['weight'] = $us_weight;
		// Dimensions
		/*$us_dimensions = '';
		preg_match('/\<tr[^>]+\>\<td[^>]+\>梱包サイズ\<\/td\>\<td[^>]+\>(.*?)\<\/td\>\<\/tr\>/si',$html_jp,$matches_jp);
		if(isset($matches_jp) && count($matches_jp) > 0){
			$us_dimensions = isset($matches_jp) && count($matches_jp) > 0 ? clean_str($matches_jp[1]) : "";
			$result['dimensions'] = $us_dimensions;
		}
		else{*/
		preg_match('/\<th[^>]+\>[^<]+Product Dimensions[^<]+\<\/th\>[^<]+\<td[^>]+\>([^<]+)\<\/td\>/si',$html_us,$matches_us);
		if(!isset($matches_us) || count($matches_us) == 0)
			preg_match('/\<li\>[^<]*\<b\>[^<]*Product Dimensions:[^<]*\<\/b\>(.*?)\<\/li\>/si',$html_us,$matches_us);
		$us_dimensions = isset($matches_us) && count($matches_us) > 0 ? clean_str($matches_us[1]) : "";
		$result['dimensions'] = $us_dimensions;
		if($us_weight=="" && strpos($us_dimensions, ";")!==false){
			$dim_parts = explode(";",$us_dimensions);
			$result['dimensions'] = clean_str($dim_parts[0]);
			$result['weight'] = clean_str($dim_parts[1]);
		}
		// }
		// Shipping Rate
		$us_shipping = get_shipping_table_value($user_settings,$us_weight,$us_dimensions,$category);
		$result['shipping'] = $us_shipping;
		// Prices 3 US
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
		$result['price_us'] = $price_us;
		$result['price_us_prime'] = $price_us_prime;
		$result['prices_3_us'] = implode(";", $prices_3);
		//
		preg_match('/\"subtext\":\"Eligible for (Amazon Prime) shipping benefits\"/si',$html_us,$matches_us);
		$amazon_prime = isset($matches_us) && count($matches_us) > 0 ? "1" : "0";
		$result['amazon_prime'] = $amazon_prime;
		// Prices 3 JP
		if($commodity!=""){
			preg_match('/href=\"\/gp\/offer-listing\/([^\/]+)\/[^"]+\"/si', $html_jp, $matches_jp);
			if(isset($matches_jp) && count($matches_jp)>0){
				$asin_in_url = isset($matches_jp[1]) ? $matches_jp[1] : $asin;
				$prices_3 = [];
				$prices_url = "https://www.amazon.co.jp/gp/offer-listing/$asin_in_url/ref=dp_olp_new?ie=UTF8&condition=new";
				$prices_html = get_html($prices_url);
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
				$result['days_to_ship'] = $expected_arrival_;
				$result['days_to_ship_prime'] = $expected_arrival_prime;
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
				$result['days_to_ship'] = $amazon_prime=="1" ? "PRIME" : "";
				$result['days_to_ship_prime'] = $amazon_prime=="1" ? "PRIME" : "";
			}
		}
		//
		$amazon_commission = (($price_us*US_JP_RATE)) * 0.15;
		$result['amazon_commission'] = $amazon_commission;
		$expected_profit = ($price_us*US_JP_RATE) - $price_jp - ($us_shipping) - ($amazon_commission);
		$result['expected_profit'] = $expected_profit;
		// Sellers number
		$offer_listing_url = "https://www.amazon.com/gp/offer-listing/$asin/ref=dp_olp_new?ie=UTF8&condition=new&startIndex=2000";
		$offer_listing_html = get_html($offer_listing_url);
		preg_match('/\<li class=\"a-selected\"\>.*?Page\<\/span\>([0-9]+)\<span/si', $offer_listing_html, $offer_listing_matches);
		$offers_last_page = isset($offer_listing_matches) ? $offer_listing_matches[1] : 0;
		preg_match_all('/(olpOffer\")/si', $offer_listing_html, $offer_listing_matches2);
		$offer_listing_count = isset($offer_listing_matches2) ? count($offer_listing_matches2[1]) : 0;
		if($offer_listing_count==0 || $offers_last_page==0){
			preg_match('/\<a href=\"\/gp\/offer-listing\/[^"]+\"\>.*?([0-9]+).*?\<\/a\>/si',$html_us,$matches_us);
			if(!isset($matches_us) || count($matches_us) == 0){
				preg_match('/\>([0-9]+)&nbsp;new\</si',$html_us,$matches_us);
				if(!isset($matches_us) || count($matches_us) == 0){
					preg_match('/([0-9]+) New\s?\</si',$html_us,$matches_us);
					if(!isset($matches_us) || count($matches_us) == 0){
						preg_match('/\<b\>New\<\/b\> \(([0-9]+)\)/si',$html_us,$matches_us);
					}
				}
			}
			$us_sellers_num = isset($matches_us) && count($matches_us) > 0 ? $matches_us[1] : "0";
		}
		else
			$us_sellers_num = $offer_listing_count + ((intval($offers_last_page)-1) * 10);
		$result['num_of_sellers'] = $us_sellers_num;
		// Release Date
		preg_match('/\<tr class=\"date-first-available\"\>\<td class=\"label\"\>[^<]+\<\/td\>\<td class=\"value\"\>([^<]+)\<\/td\>\<\/tr\>/si',$html_jp,$matches_jp);
		if(!isset($matches_jp) || count($matches_jp) == 0)
			preg_match('/\<li\>[^<]*\<b\>[^<]*発売日[^<]*\<\/b\>(.*?)\<\/li\>/si',$html_jp,$matches_jp);
		// <li><b> 発売日：</b> 2000/7/1</li>
		$release_date = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
		$result['release_date'] = $release_date;
		echo "<PRE>";
		print_r($result);
		echo "</PRE>";
		// exit;
		if($mode==STATUS_NEW){
			$result['status'] = STATUS_DEFAULT;
			replace(TABLE_PRODUCTS,$result,$link);
		}
		else if($mode==STATUS_SELLING){
			insert_on_duplicate(TABLE_PRODUCTS,$result,$link);
			$sell_asins[] = $asin;
		}
    }
    if(!empty($sell_asins))
    	sell($sell_asins,true);
    /*exec('nohup php -qf /var/www/html/amazon-tool/service/fix_manager.php "comm" >/dev/null 2>&1 &');
    exec('nohup php -qf /var/www/html/amazon-tool/service/fix_manager.php "profit" >/dev/null 2>&1 &');
    exec('nohup php -qf /var/www/html/amazon-tool/service/fix_manager.php "ship" >/dev/null 2>&1 &');*/
?>
