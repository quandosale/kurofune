<?php
	require_once(dirname(dirname(__FILE__))."/backend/db.php");
	// 3: New Extraction 	1:Selling
	$mode = isset($argv) && count($argv) > 1 ? $argv[1] : "";
	if($mode=="")
		exit;
	//
	$link = connect();
	if($mode=="profit"){
		general_query("update products set expected_profit=((price_us*".US_JP_RATE.") - price_jp - shipping - amazon_commission)",$link);
		exit;
	}
	else if($mode=="comm"){
		general_query("update products set amazon_commission = ((price_us*".US_JP_RATE."))*0.15;",$link);
		exit;
	}
	else if($mode=="ship")
    	// $res_count = query(TABLE_PRODUCTS,[]," WHERE shipping%5<>0 or (shipping=1500 and weight<>'') ORDER BY updated DESC",$link);
    	$res_count = query(TABLE_PRODUCTS,[],"",$link);
    else if($mode=="image_jp")
        $res_count = query(TABLE_PRODUCTS,[]," WHERE image_jp='' ORDER BY updated DESC",$link);
    else if($mode=="commodity")
        $res_count = query(TABLE_PRODUCTS,[]," WHERE commodity='' and price_jp>0 ORDER BY updated DESC",$link);
    else if($mode=="price_us")
    	// $res_count = query(TABLE_PRODUCTS,[]," WHERE price_us=0 and num_of_sellers<>0 ORDER BY updated DESC",$link);
        $res_count = query(TABLE_PRODUCTS,[]," WHERE price_us>50 ORDER BY updated DESC",$link);
    else if($mode=="price_jp")
    	// $res_count = query(TABLE_PRODUCTS,[]," WHERE price_jp_prime=0 ORDER BY updated DESC",$link);
        // $res_count = query(TABLE_PRODUCTS,[]," WHERE price_jp=0 ORDER BY updated DESC".$limit_sql,$link);
        $res_count = query(TABLE_PRODUCTS,[]," WHERE price_jp=0 ORDER BY updated DESC".$limit_sql,$link);
    else if($mode=="weight")
    	// $res_count = query(TABLE_PRODUCTS,[]," WHERE weight='' and price_jp>0 and category not like '%CDs%' ORDER BY updated DESC",$link);
    	$res_count = query(TABLE_PRODUCTS,[],"".$limit_sql,$link);
    else if($mode=="dim")
    	$res_count = query(TABLE_PRODUCTS,[]," WHERE dimensions='' and price_jp>0 and category not like '%CDs%' ORDER BY updated DESC",$link);
    else if($mode=="cat")
    	// $res_count = query(TABLE_PRODUCTS,[]," WHERE category='' and image_us<>'' ORDER BY updated DESC",$link);
    	$res_count = query(TABLE_PRODUCTS,[],"",$link);
    else{
    	echo "Wrong Arguments!";
    	exit;
    }
	//
    $total = mysqli_num_rows($res_count);
    $limit = ceil($total/EXTRACTION_THREADS);
    for($i=0;$i<EXTRACTION_THREADS;$i++){
    	$from = $i * $limit;
        if($from > $total)
            break;
    	echo "nohup php -qf /var/www/html/amazon-tool/service/fix.php ".$mode." $from $limit >/dev/null 2>&1 &\n";
    	exec("nohup php -qf /var/www/html/amazon-tool/service/fix.php ".$mode." $from $limit >/dev/null 2>&1 &");
    }
?>
