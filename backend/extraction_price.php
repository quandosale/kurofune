<?php
require_once(dirname(dirname(__FILE__))."/backend/db.php");
	require_once(dirname(dirname(__FILE__))."/backend/Client.php");

require("simple_html_dom.php");



			$name="condor3096.startdedicated.com";
			$user="amazon_tool";
			$pass="divinfo_sys";
			$db="amazon_tool";
			$con=mysqli_connect($name,$user,$pass,$db);


function gehtml($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); // Target URL
    //curl_setopt($ch, CURLOPT_PROXY, '62.210.106.172:3499');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, FALSE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function getCaptcha($url)
{
    
    $http_head = array(
        "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
        "Accept-Language:en-US,en;q=0.8",
        "Connection:keep-alive",
        "Upgrade-Insecure-Requests:1",
        "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36"
    );
    $ch        = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, FALSE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $http_head);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function downloadPage($url)
{
    $http_head = array(
        "Accept:*/*",
        "Accept-Language:en-US,en;q=0.8",
        "Connection:keep-alive",
        "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36"
    );
    $ckfile    = 'cookie_ind.txt';
    $ch        = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_PROXY, $proxy_new);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, FALSE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $http_head);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function call_amazon($url)
{
    $html2 = downloadPage($url);
    $html2 = str_get_html($html2);
    if (!empty($html2)) {
        for ($i = 1; $i <= 10; $i++) {
            $captcha_check = $html2->find("form", 0)->action;
            if ($captcha_check == '/errors/validateCaptcha') {
                $img_url = $html2->find("form[action=/errors/validateCaptcha] img", 0)->src;
                $amzn    = $html2->find("input[name=amzn]", 0)->value;
                $amzn_r  = $html2->find("input[name=amzn-r]", 0)->value;
                $amzn_pt = $html2->find("input[name=amzn-pt]", 0)->value;
                
                $text = getCaptcha("http://70.35.205.191/tesseract/test.php?images=" . urlencode($img_url));
                
                $new_url = "http://www.amazon.com/errors/validateCaptcha?amzn=" . urlencode($amzn) . "&amzn-r=" . urlencode($amzn_r) . "&amzn-pt=" . urlencode($amzn_pt) . "&field-keywords=" . $text;
                downloadPage($new_url);
                
                $html2 = downloadPage($url);
                $html2 = str_get_html($html2);
                
            } else {
                $i = 11;
            }
        }
    }
    
    return $html2;
    
}

$qry = mysql_query($con,"select asin from  products order by id asc");
while ($row = mysql_fetch_array($qry)) {
    //$title_link = $row['title_link'];
	//$asin = $row['asin'];
	
    $asin = "B005M36FU4";
    $title_link = "https://www.amazon.com/dp/B005M36FU4";
    $brand_html = call_amazon($title_link);
    
    //sleep(5);
    if (!empty($brand_html)) {
		
        $rprice=$brand_html->find('a[id=a-autoid-1-announce] span[class=a-color-base]',0)->plaintext;
		$rprice=trim($rprice);
       
		
        
		$qry2 = "update products SET
        price_us = '" . mysql_real_escape_string($rprice) . "'
        where asin = '" . mysql_real_escape_string($asin) . "'";
        
		echo $qry2.'<br>';
       // mysql_query($con,$qry2);
        //echo mysql_error();
        exit;
		
       
        
    }
    
}

?>