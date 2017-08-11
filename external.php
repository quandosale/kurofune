<?php
	require_once("backend/db.php");
	//
    authenticate_user();
	//
    $asin = isset($_GET['asin']) ? $_GET['asin'] : "";
    $site = isset($_GET['site']) ? $_GET['site'] : "";
    if($asin!=="" && $site!=""){
        // echo file_get_contents("http://www.amazon.$site/dp/$asin");
        /*$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, "http://www.check2ip.com");
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: ".getRealIpAddr(),
                                            "X_FORWARDED_FOR: ".getRealIpAddr()));
		$html = curl_exec($ch);
		echo $html;*/
        // echo get_html("http://www.amazon.$site/dp/$asin",false);
        echo get_html("http://www.amazon.$site/dp/$asin",false);
    }
?>
