<?php
	require_once(dirname(dirname(__FILE__))."/backend/db.php");
	//
	$link = connect();
	$html = get_html("http://info.finance.yahoo.co.jp/fx/");
	preg_match('/\<span id=\"USDJPY_top_bid\"\>([^<]+)\<\/span\>/si', $html, $matches);
	$USD_JPY = doubleval(get_value($matches[1]));
	if($USD_JPY > 0){
		$values = ["from"=>"USD"
					,"to"=>"JPY"
					,"rate"=>$USD_JPY];
		replace(TABLE_EXCHANGE_RATES,$values,$link);
	}
?>