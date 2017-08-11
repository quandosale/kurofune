<?php
	require_once(dirname(dirname(__FILE__))."/backend/db.php");
	require_once(dirname(dirname(__FILE__))."/backend/Client.php");
	//
	$config = array (
	  'ServiceURL' => "https://mws.amazonservices.com"
	);
	$service = new MarketplaceWebService_Client(
				     AWS_ACCESS_KEY_ID, 
				     AWS_SECRET_ACCESS_KEY, 
				     $config,
				     APPLICATION_NAME,
				     APPLICATION_VERSION);
	$xml_res = $service->request(["AWSAccessKeyId"=>AWS_ACCESS_KEY_ID
									,"Action"=>"ListOrders"
									,"SellerId"=>SELLER_ID
									,"MarketplaceId.Id.1"=>MARKETPLACE_ID
									,"CreatedAfter" => "2010-10-01T18:12:21.000Z"
									,"SignatureMethod"=>"HmacSHA256"
									,"SignatureVersion"=>"2"
									,"Timestamp"=>gmdate('Y-m-d\TH:i:s.\0\0\0\Z', time())
									,"Version"=>"2013-09-01"],AWS_SECRET_ACCESS_KEY,"/Orders/2013-09-01");
	$orders = $xml_res->ListOrdersResult->Orders->Order;
	// echo "<PRE>";print_r($xml_res);
    foreach ($orders as $order) {
    	$result = [];
    	$order_id = $order->AmazonOrderId;
    	$result['order_id'] = clean_str($order->AmazonOrderId->asXML());
    	$result['order_date'] = clean_str($order->PurchaseDate->asXML());
    	$result['country'] = $order->ShippingAddress ? clean_str($order->ShippingAddress->CountryCode->asXML()) : "";

    	$xml_res2 = $service->request(["AWSAccessKeyId"=>AWS_ACCESS_KEY_ID
									,"Action"=>"ListOrderItems"
									,"SellerId"=>SELLER_ID
									,"AmazonOrderId"=>$order_id
									,"SignatureMethod"=>"HmacSHA256"
									,"SignatureVersion"=>"2"
									,"Timestamp"=>gmdate('Y-m-d\TH:i:s.\0\0\0\Z', time())
									,"Version"=>"2013-09-01"],AWS_SECRET_ACCESS_KEY,"/Orders/2013-09-01");
    	$order_item = $xml_res2->ListOrderItemsResult->OrderItems->OrderItem;
    	// echo "<PRE>";print_r($xml_res2);
    	// Commodity
    	$result["commodity"] = clean_str($order_item->Title->asXML());
    	$asin = clean_str($order_item->ASIN->asXML());
    	$result["asin"] = $asin;
    	// Image
    	$amazon_us_url = "https://www.amazon.com/dp/".$asin;
		$amazon_jp_url = "https://www.amazon.co.jp/dp/".$asin;
		$html_us = get_html($amazon_us_url);
		$html_jp = get_html($amazon_jp_url);
		preg_match('/,\"large\":\"([^"]+)\",/si',$html_jp,$matches_jp);
		$jp_image = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
		$result['image_jp'] = $jp_image;
		preg_match('/,\"large\":\"([^"]+)\",/si',$html_us,$matches_us);
		$us_image = isset($matches_us) && count($matches_us) > 0 ? $matches_us[1] : "";
		$result['image_us'] = $us_image;
		// Ranking
		$xml_res3 = $service->request(["AWSAccessKeyId"=>AWS_ACCESS_KEY_ID
									,"Action"=>"GetMatchingProduct"
									,"SellerId"=>SELLER_ID
									,"MarketplaceId"=>MARKETPLACE_ID
									,"SignatureMethod"=>"HmacSHA256"
									,"SignatureVersion"=>"2"
									,"Timestamp"=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time())
									,"Version"=>"2011-10-01"
									,"ASINList.ASIN.1"=>$asin],AWS_SECRET_ACCESS_KEY,"/Products/2011-10-01");
		$ranks = $xml_res3->GetMatchingProductResult->Product->SalesRankings->SalesRank;
		$result['ranking'] = count($ranks) > 0 ? clean_str($ranks[0]->Rank->asXML()) : "";
		// Selling Price
		$selling_price = clean_str($order_item->ItemPrice->Amount->asXML());
		$result['selling_price'] = $selling_price;
		/*$shipping = clean_str($order_item->ShippingPrice->Amount->asXML());
		$result['shipping'] = $shipping;*/
		// Amazon Commission
		$result['amazon_commission'] = doubleval($selling_price)*0.15;
		//
    	echo "<PRE>";print_r($result);
		insert_on_duplicate(TABLE_PURCHASE,$result,$link);
    }
?>
