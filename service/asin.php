<?php
    set_time_limit(0);
    //
	require_once(dirname(dirname(__FILE__))."/backend/db.php");
	//
    // exit;
	$link = connect();
    $res = query(TABLE_ASIN_URLS,[]," WHERE status=2",$link);
    while($row = mysqli_fetch_array($res)){
        $amazon_url = $row['url'];
        $domain = strpos($row['url'], ".co.jp")!==false ? "co.jp" : "com";
        $amazon_html = get_html($row['url']);
        // Check 60 or not
        $number_in_page = 24;
        if($domain=="co.jp"){
            preg_match('/検索結果 ([0-9,]+)件中 ([0-9,]+)-([0-9,]+)件/si', $amazon_html, $matches0);
            $number_in_page = $matches0!=null && count($matches0) > 0 ? str_replace(",", "", $matches0[3]) : 24;
        }
        else{
            preg_match('/([0-9])+-([0-9])+ of ([0-9,]+) results for/si', $amazon_html, $matches0);
            $number_in_page = $matches0!=null && count($matches0) > 0 ? str_replace(",", "", $matches0[2]) : 24;
        }
        //
        if($number_in_page!=60){
            preg_match('/\<span class=\"s-layout-toggle-picker\"\>[^<]+\<a href=\"([^"]+)\"/si',$amazon_html,$amazon_matches);
            $amazon_url = isset($amazon_matches[1]) ? "http://www.amazon.$domain".str_replace("&amp;","&",$amazon_matches[1]) : $amazon_url;
            echo $amazon_url."<br>";
        }
    	$asin_url_id = $row['id'];
    	$total_pages = 1000;
    	for($page=1 ; $page <= $total_pages; $page++){
            echo $page."\n<br>";
    		$current_amazon_url = $amazon_url . "&page=$page";
    		$html = get_html($current_amazon_url);
            file_put_contents("asin.html", $html);
    		if($total_pages==1000){
                if($domain=="co.jp"){
                    preg_match('/検索結果 ([0-9,]+)件中 ([0-9,]+)-([0-9,]+)件/si', $amazon_html, $matches0);
                    $total_pages = $matches0!=null && count($matches0) > 0 ? str_replace(",", "", $matches0[1]) : 50;
                }
                else{
                    preg_match('/([0-9])+-([0-9])+ of ([0-9,]+) results for/si', $amazon_html, $matches0);
                    $total_pages = $matches0!=null && count($matches0) > 0 ? str_replace(",", "", $matches0[3]) : 50;
                }
    			$total_pages = ceil($total_pages/60.0);
    			echo "Total Pages: ".$total_pages."<br>\n";
    		}
	    	preg_match_all('/\<li id=\"result_[0-9]+\" data-asin=\"([^"]+)\"/s', $html, $matches);
	    	$asins = array_unique($matches[1]);
	    	$count = 1;
	    	foreach ($asins as $asin) {
	    		if($count++ > 60)
	    			break;
	    		$values = ["asin" => $asin,"asin_url_id" => $asin_url_id];
                $values['user_id'] = $row['user_id'];
				insert(TABLE_ASINS,$values,$link);
	    	}
    	}
        update(TABLE_ASIN_URLS,["status"=>1]," WHERE id='".$asin_url_id."'",$link);
    }
	echo "Done";
?>
