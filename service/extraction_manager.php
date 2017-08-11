<?php
	require_once(dirname(dirname(__FILE__))."/backend/db.php");
	require_once(dirname(dirname(__FILE__))."/backend/Client.php");
	// 3: New Extraction 	1:Selling
	$mode = isset($argv) && count($argv) > 1 ? $argv[1] : "";
	if($mode=="")
		exit;
	//
	$link = connect();
    $res_count = query(TABLE_PRODUCTS,[]," WHERE status=".$mode." ORDER BY updated DESC",$link);
    $total = mysqli_num_rows($res_count);
    $limit = ceil($total/EXTRACTION_THREADS);
    for($i=0;$i<EXTRACTION_THREADS;$i++){
    	$from = $i * $limit;
    	echo "nohup php -qf /var/www/html/amazon-tool/service/extraction.php ".$mode." $from $limit >/dev/null 2>&1 &\n";
    	exec("nohup php -qf /var/www/html/amazon-tool/service/extraction.php ".$mode." $from $limit >/dev/null 2>&1 &");
    }
?>
