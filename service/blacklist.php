<?php
	require_once(dirname(dirname(__FILE__))."/backend/db.php");
	//
	$link = connect();
    $res = query(TABLE_BLACKLIST,[]," WHERE status=".STATUS_NEW,$link);
    while($row = mysqli_fetch_array($res)){
    	$asin = $row['asin'];
		$result = [];
		$amazon_us_url = "https://www.amazon.com/dp/$asin";
		$amazon_jp_url = "https://www.amazon.co.jp/dp/$asin";
		$html_us = get_html($amazon_us_url);
		file_put_contents("sample.html",$html_us);
		$html_jp = get_html($amazon_jp_url);
		$is_jp = strpos($html_jp,"入力したURLが当サイトのページと一致しません")===false;
		preg_match('/,\"large\":\"([^"]+)\",/si',$html_us,$matches_us);
		$us_image = isset($matches_us) && count($matches_us) > 0 ? $matches_us[1] : "";
		$result['image'] = $us_image;
		$result['asin'] = $asin;
		// Commodity
		preg_match('/\<meta name=\"keywords\" content=\"([^\",]+)[\",]/si', $html_jp, $matches_jp);
		$commodity = isset($matches_jp) && count($matches_jp) > 0 ? $matches_jp[1] : "";
		$result['commodity'] = $commodity;
		$result['user_id'] = $row['user_id'];
		$result['status'] = STATUS_DEFAULT;
		replace(TABLE_BLACKLIST,$result,$link);
		echo "<PRE>";
		print_r($result);
		echo "</PRE>";
    }
?>
