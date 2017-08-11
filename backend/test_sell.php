<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	//
	require_once("db.php");
	require_once("Client.php");
	//
	$sell_asin = isset($_GET["sell_asin"]) ? $_GET["sell_asin"] : "";
	$feed_id = isset($_GET["feed_id"]) ? $_GET["feed_id"] : "";
	if($sell_asin!=""){
		$link = connect();
		$res = query(TABLE_PRODUCTS,[]," WHERE status=".STATUS_SELLING." ORDER BY updated DESC",$link);
	    $sell_asins = [];
	    while($row = mysqli_fetch_array($res)){
	    	$asin = $row['asin'];
	    	$sell_asins[] = $asin;
	    }
		sell($sell_asins,true);
	}
	if($feed_id!="")
		info($feed_id);

	function info($id){
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
									,"Action"=>"GetFeedSubmissionResult"
									,"SellerId"=>SELLER_ID
									,"Marketplace"=>MARKETPLACE_ID
									,"SignatureMethod"=>"HmacSHA256"
									,"SignatureVersion"=>"2"
									,"Timestamp"=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time())
									,"Version"=>"2009-01-01"
									,"FeedSubmissionId"=>$id],AWS_SECRET_ACCESS_KEY,"/Feeds/2009-01-01");
		echo "<PRE>";
		print_r($xml_res);
	}
?>