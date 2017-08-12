<?php
	require_once(dirname(__FILE__)."/proxies.php");
	//
	error_reporting(E_ALL);
	ini_set("display_errors", 0);
	//
	session_start();
    //
    define("STATUS_DEFAULT",0);
    define("STATUS_SELLING",1);
    define("STATUS_BL",2);
    define("STATUS_NEW",3);
    define("PAGE_COUNT",100);
    define("OZ_to_GM",28.35);
    define("LB_to_GM",453.59237);
    define("KG_to_GM",1000);
    define("INCH_to_CM",2.54);
    define("EXTRACTION_THREADS",70);
    //
    define("TOOL_URL","http://tool.kurofune.club");
    define("ADMIN_URL","http://user-section.kurofune.club");
    //
	define("DB_HOST","localhost");
	define("DB_NAME","amazon_tool");
	define("DB_USER","root");
	define("DB_PASS","");
	define("TABLE_ASIN_URLS","asin_urls");
	define("TABLE_ASINS","asins");
	define("TABLE_USERS","users");
	define("TABLE_PRODUCTS","products");
	define("TABLE_SETTINGS","settings");
	define("TABLE_EXCHANGE_RATES","exchange_rates");
	define("TABLE_OPTIONS","options");
	define("TABLE_BLACKLIST","blacklist");
	define("TABLE_BLACKLIST_KEYWORDS","blacklist_keywords");
	define("TABLE_PURCHASE","purchase");
	//
	$user_settings = get_shipping_rates();
	//
	// define("AWS_ACCESS_KEY_ID","AKIAJEVR5OFYXURWMNDA");
	// define("AWS_ACCESS_KEY_ID","AKIAI44IKS4WO2G6GJ7A");
	define("AWS_ACCESS_KEY_ID",$user_settings['access_key']);
	define("GLOBAL_COMP_AWS_ACCESS_KEY_ID","AKIAIIGO6VKJTMUXYZ3A");
    // define("AWS_SECRET_ACCESS_KEY","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");
    // define("AWS_SECRET_ACCESS_KEY","o6KNSQlpG2aEjF3DMN866Tjtza8RLCC/zo+0ThNZ");
    define("AWS_SECRET_ACCESS_KEY",$user_settings['secret_key']);
    define("APPLICATION_NAME","AmazonTool");
    define("APPLICATION_VERSION","1");
    // define("SELLER_ID", 'AA2YWD5EMYVVD');
    // A3HKJSKJV1Q606
    define("SELLER_ID", $user_settings['seller_id']);
    define("MARKETPLACE_ID", 'ATVPDKIKX0DER');
	//
	$country = get_param('country','USA');
	//
	$link = connect();
	/* $res = query(TABLE_EXCHANGE_RATES,array()," WHERE `from`='USD' and `to`='JPY'",$link); */
	
	$q3="select * from exchange_rates WHERE `from`='USD' and `to`='JPY'";
	$res=mysqli_query($link,$q3);
	$row = mysqli_fetch_array($res);
	define("US_JP_RATE",get_value($row["rate"]));
	//
	$cmd = get_param("cmd");
	if($cmd=="logout"){
		unset($_SESSION['user']);
		unset($_SESSION['user_id']);
		header("Location: login.php");
	}
	//
	function clean_str($str){
		return trim(preg_replace('/\&/','',preg_replace('/\n/', "",preg_replace('/\t/', "",preg_replace('/\s{2,}/', "",strip_tags($str))))));
	}
	function get_value($val,$default=""){
		return isset($val) ? $val : $default;
	}
	function get_param($param_name,$default=""){
		return isset($_REQUEST[$param_name]) && $_REQUEST[$param_name]!="" ? $_REQUEST[$param_name] : $default;
	}
	function authenticate(){
		if(isset($_SESSION['admin']) && $_SESSION['admin']!=""){
			return true;
		}
		header("Location: operator-login.php");
	}
	function authenticate_user(){
		if(isset($_SESSION['user']) && $_SESSION['user']!=""){
			return true;
		}
		header("Location: login.php");
	}
	function get_html($url,$with_proxy=true,$post="",$cookies=""){
		global $proxies,$proxies_jp,$user_pass,$proxy_server_index;
		//
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		if($post!=""){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$server_output = "";
		$proxy_servers = array("fr","au","de","nl","sg","us-ny","us-il","uk","us","us-fl","us-ca","us-dc","ch");
		$proxy_server_index = 0;
		$fail = 0;
		do{
			if($with_proxy){
				/*curl_setopt($ch, CURLOPT_PROXY, "http://".$proxy_servers[$proxy_server_index++].".proxymesh.com:31280");
			    curl_setopt($ch, CURLOPT_PROXYUSERPWD, "kurofune:goUni1013&");*/
			    curl_setopt($ch, CURLOPT_PROXY, $proxies[($proxy_server_index++)%count($proxies)]);
			    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
			}
			else{
				curl_setopt($ch, CURLOPT_PROXY, $proxies_jp[rand(0,count($proxies_jp)-1)]);
		    }
			$server_output = curl_exec ($ch);
		}while($fail++<100 && ($server_output=="" || stripos($server_output, "Robot Check")!==false || stripos($server_output, "Captcha")!==false));
		/*if($server_output=="" || stripos($server_output, "Robot Check")!==false || stripos($server_output, "Captcha")!==false){
			curl_setopt($ch, CURLOPT_PROXY, "");
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, "");
			$server_output = curl_exec ($ch);
		}*/
		curl_close ($ch);
		/*if($with_proxy)
			sleep(5);*/
		return $server_output;
	}
	function getRealIpAddr(){
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }	
    function days_diff($date1,$date2){
		$datediff = strtotime($date2) - strtotime($date1);
		return floor($datediff / (60 * 60 * 24));
    }
    function blacklisted($item,$blacklist){
    	foreach ($blacklist as $bl_item) {
    		if(stripos($item,$bl_item)!==false)
    			return true;
    	}
    	return false;
    }
    function get_shipping_rates($user_id=''){
    	if($user_id=="")
    		$user_id = $_SESSION['user_id'];
    	$link = connect();
    	/* $res = query(TABLE_SETTINGS,array()," WHERE user_id=".$user_id,$link); */
		$q="select * from settings WHERE status=1 and user_id=".$_SESSION['user_id'];
		$res=mysqli_query($link,$q);
    	$row = mysqli_fetch_array($res);
    	return $row;
    }
    function get_shipping_table_value($user_settings,$us_weight,$us_dimensions,$category){
    	$table = array();
    	// Category 1
    	$table[0] = array("SAL"=>array("100"=>180,"200"=>280,"300"=>380,"400"=>480,"500"=>580,"600"=>680,"700"=>780,"800"=>880,
		
		"900"=>980,"1000"=>1080,"1100"=>1180,"1200"=>1280,"1300"=>1380,"1400"=>1480,"1500"=>1580,"1600"=>1680,"1700"=>1780,"1800"=>1880,"1900"=>1980,"2000"=>2080)
    				,"E Bucket"=>array("50"=>560,"100"=>635,"150"=>710,"200"=>785,"250"=>860,"300"=>935,"400"=>1085,"500"=>1235,"600"=>1385,"700"=>1535,"800"=>1685,"900"=>1835,"1000"=>1985,"1250"=>2255,"1500"=>2525,"1750"=>2795,"2000"=>3065)
    				,"EMS"=>array("500"=>2000,"600"=>2180,"700"=>2360,"800"=>2540,"900"=>2720,"1000"=>2900,"1250"=>3300,"1500"=>3700,"1750"=>4100,"2000"=>4500)
    				,"D mail"=>array("50"=>120,"100"=>190,"150"=>260,"200"=>330,"250"=>400,"300"=>470,"350"=>540,"400"=>610,"450"=>680,"500"=>750,"550"=>820,"600"=>890,"650"=>960,"700"=>1030,"750"=>1100,"800"=>1170,"850"=>1240,"900"=>1310,"950"=>1380,"1000"=>1450,"1250"=>1655,"1500"=>1860,"1750"=>2065,"2000"=>2270));
    	// Category 2
    	$table[1] = array("SAL"=>array("3000"=>5000,"4000"=>6150,"5000"=>7300,"6000"=>8350,"7000"=>9400,"8000"=>10450,"9000"=>11500,"10000"=>12550,"11000"=>13250,"12000"=>13950,"13000"=>14650,"14000"=>15350,"15000"=>16050,"16000"=>16750,"17000"=>17450,"18000"=>18150,"19000"=>18850,"20000"=>19550,"21000"=>20250,"22000"=>20950,"23000"=>21650,"24000"=>22350,"25000"=>23060,"26000"=>23750,"27000"=>24450,"28000"=>25150,"29000"=>25850,"30000"=>26550)
    				,"EMS"=>array("2500"=>5200,"3000"=>5900,"3500"=>6600,"4000"=>7300,"4500"=>8000,"5000"=>8700,"5500"=>9400,"6000"=>10100,"7000"=>11200,"8000"=>12300,"9000"=>13400,"10000"=>14500,"11000"=>15600,"12000"=>16700,"13000"=>17800,"14000"=>18900,"15000"=>20000,"16000"=>21100,"17000"=>22200,"18000"=>23300,"19000"=>24400,"20000"=>25500,"21000"=>26600,"22000"=>27700,"23000"=>28800,"24000"=>29900,"25000"=>31000,"26000"=>32100,"27000"=>33200,"28000"=>34300,"29000"=>35400,"30000"=>36500));
    	//////////
    	$us_shipping = 0;
    	if(stripos($category,"dvd")!==false 
    		|| stripos($category,"ブルーレイ")!==false 
    		|| stripos($category,"ミュージック")!==false 
    		|| stripos($category,"ソフト")!==false
    		|| stripos($category,"PCゲーム*/")!==false){
			//music/DVD/Bluelay/PC game/TV game
        	$us_shipping = $user_settings["others_shipping_rate"];
        }
		else if($us_weight==""){
			$us_shipping = $user_settings["others2_shipping_rate"];
		}
		else{
			/*preg_match('/([0-9\.]+) x ([0-9\.]+) x ([0-9\.]+)/si',$us_dimensions,$dim_matches);
			$dim1 = stripos($us_dimensions,"inch")!==false ? round($dim_matches[1] * INCH_to_CM,1) : round($dim_matches[1],1);
            $dim2 = stripos($us_dimensions,"inch")!==false ? round($dim_matches[2] * INCH_to_CM,1) : round($dim_matches[2],1);
            $dim3 = stripos($us_dimensions,"inch")!==false ? round($dim_matches[3] * INCH_to_CM,1) : round($dim_matches[3],1);
            $total_dim = $dim1+$dim2+$dim3;*/
            $unit = strpos($us_weight, "pound")!==false ? LB_to_GM : (strpos($us_weight, "ounces")!==false ? OZ_to_GM : (strpos($us_weight, "Kg")!==false ? KG_to_GM : 1));
            $total_weight = round($us_weight*$unit);
            // Package Weight
            if($total_weight <= 500)
            	$total_weight += $user_settings['weight_less'];
            else if($total_weight > 500 && $total_weight <= 1000)
            	$total_weight += $user_settings['weight_between'];
            else if($total_weight > 1000)
            	$total_weight += $user_settings['weight_more'];
            //
            if($total_weight < 2000 /*&& $total_dim < 80 && ($dim1<60 && $dim2<60 && $dim3<60)*/){
            	// Category 1
            	$values = $table[0][$user_settings['standard_type']];
            	foreach ($values as $weight_key => $shipping_value) {
            		if($total_weight <= $weight_key){
            			$us_shipping = $shipping_value + $user_settings['commission'];
            			break;
            		}
            		$us_shipping = $shipping_value + $user_settings['out_commission'];
            	}
            }
            else{
            	// Category 2
            	$values = $table[1][$user_settings['out_standard_type']];
            	foreach ($values as $weight_key => $shipping_value) {
            		if($total_weight <= $weight_key){
            			$us_shipping = $shipping_value + $user_settings['out_commission'];
            			break;
            		}
            		$us_shipping = $shipping_value + $user_settings['out_commission'];
            	}
            }
		}
		return $us_shipping;
    }
    function get_prices3_html($data,$currency){
    	$html = "";
    	$big_parts = explode(";", $data);
    	foreach ($big_parts as $big_part) {
    		$small_parts = explode(":", $big_part);
    		$price = $small_parts[0];
    		if($price==0)
    			continue;
    		$prime = $small_parts[1];
    		$shipping = $small_parts[2];
    		$html .= "Price: $currency$price".($prime==1 ? " (PRIME)": "").", Shipping: $currency$shipping\n";
    	}
    	return $html;
    }

    function sell($asins,$is_update=false){
    	$products = array();
    	foreach ($asins as $asin) {
    		$product = array();
			$link = connect();
			$res = query(TABLE_PRODUCTS,array()," WHERE `asin`='$asin' and user_id=".$_SESSION['user_id'],$link);
			$row = mysqli_fetch_array($res);
			//
			$user_settings = get_shipping_rates($row['user_id']);
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
			// Sell Product
			$product['asin'] = $asin;
			$product['title'] = $row["commodity"];
			$product['description'] = $user_settings["note"];
			$product['MSRP'] = $row["price_us"];
			preg_match('/([0-9\.]+) x ([0-9\.]+) x ([0-9\.]+)/si',$row['dimensions'],$dim_matches);
	        $dim1 = stripos($row['dimensions'],"inch")!==false ? round($dim_matches[1] * INCH_to_CM,1) : round($dim_matches[1],1);
	        $dim2 = stripos($row['dimensions'],"inch")!==false ? round($dim_matches[2] * INCH_to_CM,1) : round($dim_matches[2],1);
	        $dim3 = stripos($row['dimensions'],"inch")!==false ? round($dim_matches[3] * INCH_to_CM,1) : round($dim_matches[3],1);
	        $unit = strpos($row['weight'], "pound")!==false ? LB_to_GM : (strpos($row['weight'], "ounces")!==false ? OZ_to_GM : (strpos($row['weight'], "Kg")!==false ? KG_to_GM : 1)) ;
	        $weight = round($row['weight']*$unit);
	        $product['dim1'] = $dim1;
	        $product['dim2'] = $dim2;
	        $product['dim3'] = $dim3;
	        $product['weight'] = $weight;
			
			//  Quantity
			$product['quantity'] = $user_settings['num_of_articles'];
			$product['expected_arrival'] = trim(str_replace("days","",$user_settings['handling_time']));

			// Price
			$sell_price = doubleval($row['price_us']);
			$lowest_price = $sell_price;
			if($user_settings['minus_plus']=="minus")
				$lowest_price -= doubleval($user_settings['lowest_price']);
			else if($user_settings['minus_plus']=="plus")
				$lowest_price += doubleval($user_settings['lowest_price']);
			$final_sell_price = $user_settings['based_on_type']=="1" ? $lowest_price : $sell_price;
			$product['selling_price'] = $final_sell_price;
			//
			$products[] = $product;
		}
		//
		$messages1 = "";
		$messages2 = "";
		$messages3 = "";
		$index = 1;
		foreach ($products as $product) {
			$messages1 .= '<Message>
					    <MessageID>'.$index.'</MessageID>
					    <OperationType>Update</OperationType>
					    <Product>
					      <SKU>'.$product['asin'].'</SKU>
					      <StandardProductID>
					        <Type>ASIN</Type>
					        <Value>'.$product['asin'].'</Value>
					      </StandardProductID>
					      <ProductTaxCode>A_GEN_NOTAX</ProductTaxCode>
					      <DescriptionData>
					        <Title>'.$product['title'].'</Title>
					        <Description>'.$product['description'].'</Description>
					        <ItemDimensions>
					        	<Length unitOfMeasure="CM">'.$product['dim1'].'</Length>
					        	<Width unitOfMeasure="CM">'.$product['dim2'].'</Width>
					        	<Height unitOfMeasure="CM">'.$product['dim3'].'</Height>
					        	<Weight unitOfMeasure="GR">'.$product['weight'].'</Weight>
					        </ItemDimensions>
					        <MSRP currency="USD">'.$product['MSRP'].'</MSRP>
					      </DescriptionData>
					    </Product>
					  </Message>';
		  	$messages2 .= '<Message>
						<MessageID>'.$index.'</MessageID> 
						<OperationType>Update</OperationType> 
						<Inventory>
						<SKU>'.$product['asin'].'</SKU> 
						<Quantity>'.$product['quantity'].'</Quantity> 
						<FulfillmentLatency>'.$product['expected_arrival'].'</FulfillmentLatency> 
						</Inventory>
						</Message>';
			$messages3 .= '<Message>
						<MessageID>'.$index.'</MessageID> 
						<Price>
						<SKU>'.$product['asin'].'</SKU> 
						<StandardPrice currency="USD">'.$product['selling_price'].'</StandardPrice> 
						</Price>
						</Message>';
			$index++;
		}
		if(!empty($products)){
			// Do Requests
			if(!$is_update){
				$req_body = '<?xml version="1.0" encoding="iso-8859-1"?>
				<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
				    xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
				  <Header>
				    <DocumentVersion>1.01</DocumentVersion>
				    <MerchantIdentifier>'.SELLER_ID.'</MerchantIdentifier>
				  </Header>
				  <MessageType>Product</MessageType>
				  <PurgeAndReplace>false</PurgeAndReplace>
				  '.$messages1.'
				</AmazonEnvelope>';
				$xml_res = $service->request(array("AWSAccessKeyId"=>AWS_ACCESS_KEY_ID
											,"Action"=>"SubmitFeed"
											,"FeedType"=>"_POST_PRODUCT_DATA_"
											,"SellerId"=>SELLER_ID
											,"MarketplaceIdList.Id.1"=>MARKETPLACE_ID
											,"SignatureMethod"=>"HmacSHA256"
											,"SignatureVersion"=>"2"
											,"Timestamp"=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time())
											,"Version"=>"2009-01-01"
											,"ContentMD5Value"=>$service->getContentMd5($req_body)),AWS_SECRET_ACCESS_KEY,"/Feeds/2009-01-01","POST",$req_body);
				echo "<PRE>";
				print_r($xml_res);
			}
			// Quantity
			$req_body = '<?xml version="1.0" encoding="utf-8" ?> 
	<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
	<Header>
	<DocumentVersion>1.01</DocumentVersion> 
	<MerchantIdentifier>'.SELLER_ID.'</MerchantIdentifier> 
	</Header>
	<MessageType>Inventory</MessageType> 
	'.$messages2.'
	</AmazonEnvelope>';
			$xml_res = $service->request(array("AWSAccessKeyId"=>AWS_ACCESS_KEY_ID
											,"Action"=>"SubmitFeed"
											,"FeedType"=>"_POST_INVENTORY_AVAILABILITY_DATA_"
											,"SellerId"=>SELLER_ID
											,"MarketplaceIdList.Id.1"=>MARKETPLACE_ID
											,"SignatureMethod"=>"HmacSHA256"
											,"SignatureVersion"=>"2"
											,"Timestamp"=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time())
											,"Version"=>"2009-01-01"
											,"ContentMD5Value"=>$service->getContentMd5($req_body)),AWS_SECRET_ACCESS_KEY,"/Feeds/2009-01-01","POST",$req_body);
			/*echo "<PRE>";
			print_r($xml_res);*/

			// Price
			$req_body = '<?xml version="1.0" encoding="utf-8" ?> 
	<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
	<Header>
	<DocumentVersion>1.01</DocumentVersion> 
	<MerchantIdentifier>'.SELLER_ID.'</MerchantIdentifier> 
	</Header>
	<MessageType>Price</MessageType> 
	'.$messages3.'
	</AmazonEnvelope>';
			$xml_res = $service->request(array("AWSAccessKeyId"=>AWS_ACCESS_KEY_ID
										,"Action"=>"SubmitFeed"
										,"FeedType"=>"_POST_PRODUCT_PRICING_DATA_"
										,"SellerId"=>SELLER_ID
										,"MarketplaceIdList.Id.1"=>MARKETPLACE_ID
										,"SignatureMethod"=>"HmacSHA256"
										,"SignatureVersion"=>"2"
										,"Timestamp"=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time())
										,"Version"=>"2009-01-01"
										,"ContentMD5Value"=>$service->getContentMd5($req_body)),AWS_SECRET_ACCESS_KEY,"/Feeds/2009-01-01","POST",$req_body);
			/*echo "<PRE>";
			print_r($xml_res);*/
		}
	}
	///////////////////////////////////////////////

	function connect(){
		global $link;
		if($link==null)
			$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		return $link;

	}

	function insert($table_name,$values,$link){
		$sql1 = "";
		$sql2 = "";
		foreach ($values as $key => $value) {
			$sql1 .= ",`$key`";
			$sql2 .= ",'".str_replace("'", "\'", $value)."'";
		}
		if($sql1 != "" && $sql2 != ""){
			$sql1 = substr($sql1, 1);
			$sql2 = substr($sql2, 1);
			mysqli_query($link, "INSERT INTO ".$table_name."(".$sql1. ") VALUES (" . $sql2.")");
		}
	}

	function replace($table_name,$values,$link){
		$sql1 = "";
		$sql2 = "";
		foreach ($values as $key => $value) {
			$sql1 .= ",`$key`";
			$sql2 .= ",'".str_replace("'", "\'", $value)."'";
		}
		if($sql1 != "" && $sql2 != ""){
			$sql1 = substr($sql1, 1);
			$sql2 = substr($sql2, 1);
			mysqli_query($link, "REPLACE INTO ".$table_name."(".$sql1. ") VALUES (" . $sql2.")");
		}
	}

	function update($table_name,$values,$where,$link){
		$sql1 = "";
		foreach ($values as $key => $value) {
			$sql1 .= ",$key = '".str_replace("'", "\'", $value)."'";
		}
		if($sql1!=""){
			$sql1 = substr($sql1, 1);
			mysqli_query($link, "UPDATE ".$table_name." SET ".$sql1." ".$where);
		}
	}

	function insert_on_duplicate($table_name,$values,$link){
		$sql1 = "";
		$sql2 = "";
		foreach ($values as $key => $value) {
			$sql1 .= ",`$key`";
			$sql2 .= ",'".str_replace("'", "\'", $value)."'";
		}
		$sql3 = "";
		foreach ($values as $key => $value) {
			if($key=="status")
				continue;
			$sql3 .= ",$key = '".str_replace("'", "\'", $value)."'";
		}
		if($sql1 != "" && $sql2 != "" && $sql3 != ""){
			$sql1 = substr($sql1, 1);
			$sql2 = substr($sql2, 1);
			$sql3 = substr($sql3, 1);
			echo "INSERT INTO ".$table_name."(".$sql1. ") VALUES (" . $sql2.") ON DUPLICATE KEY UPDATE ".$sql3;
			mysqli_query($link, "INSERT INTO ".$table_name."(".$sql1. ") VALUES (" . $sql2.") ON DUPLICATE KEY UPDATE ".$sql3);
		}
	}

	function delete($table_name,$where,$link){
		mysqli_query($link, "DELETE FROM ".$table_name.$where);
	}

	function truncate($table_name,$link){
		mysqli_query($link, "TRUNCATE TABLE ".$table_name);
	}

	function query($table_name,$fields,$where,$link,$limit=""){
		$sql1 = "";
		foreach ($fields as $key) {
			$sql1 .= ",$key";
		}
		if($sql1=="")
			$sql1 = "*";
		return mysqli_query($link, "SELECT ".$sql1." FROM ".$table_name." ".$where.$limit);
	}

	function general_query($sql,$link){
		mysqli_query($link, $sql);
	}
?>
