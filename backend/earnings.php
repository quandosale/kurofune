<?php
	require_once("db.php");
	//
	$cmd = get_param('cmd');
	//
	$link = connect();
	$cal_from = get_param("cal_from","2014-02-20");
	$cal_to = get_param("cal_to",date("Y-m-d"));
	$sql = "select sum(`selling_price`) as sell_sum,sum(`amazon_commission`) as comm_sum,sum(`purchase_price`) as purchase_sum,sum(`shipping`) as shipping_sum,count(*) as c from purchase where `order_date` > '$cal_from' and `order_date` < '$cal_to' and user_id=".$_SESSION['user_id'];
	$aggregate_res = mysqli_query($link,$sql);
	$aggregate_row = mysqli_fetch_array($aggregate_res);
?>