<?php
	require_once("db.php");
	//
	$cmd = get_param('cmd');
	$where_sql = "";
	//
	if($cmd=="IMPORT"){
		$link = connect();
		$csv_file = $_FILES["csv_file"]["tmp_name"];
		$csv_rows = file($csv_file,FILE_IGNORE_NEW_LINES);
		//print_r($csv_rows);exit;
		foreach ($csv_rows as $asin) {
			$values = ["asin" => $asin];
			insert(TABLE_SALES,$values,$link);
		}
		//header("Location: ../extraction.php?import_success=1");
	}
	else if($cmd=="EXECUTE ACTION"){
		$link = connect();
		$delete_asins = get_param("delete");
		// DELETE
		if(isset($delete_asins)){
			$delete_sql = "";
			foreach ($delete_asins as $delete_asin) {
				$delete_sql .= ",'".trim($delete_asin)."'";
			}
			if($delete_sql!=""){
				$delete_sql = substr($delete_sql, 1);
				delete(TABLE_SALES," WHERE asin in ($delete_sql)",$link);
			}
		}
		// SELLING
		$start_selling_asins = get_param("start_selling");
		if(isset($start_selling_asins)){
			$start_selling_sql = "";
			foreach ($start_selling_asins as $start_selling_asin) {
				$start_selling_sql .= ",'".trim($start_selling_asin)."'";
			}
			if($start_selling_sql!=""){
				$start_selling_sql = substr($start_selling_sql, 1);
				$values = ["status" => STATUS_SELLING];
				update(TABLE_SALES,$values," WHERE asin in ($start_selling_sql)",$link);
			}
		}
		// BL
		$bl_asins = get_param("bl");
		if(isset($bl_asins)){
			$bl_sql = "";
			foreach ($bl_asins as $bl_asin) {
				$bl_sql .= ",'".trim($bl_asin)."'";
			}
			if($bl_sql!=""){
				$bl_sql = substr($bl_sql, 1);
				$values = ["status" => STATUS_BL];
				update(TABLE_SALES,$values," WHERE asin in ($bl_sql)",$link);
			}
		}
		//header("Location: ../extraction.php?action_success=1");
	}
	else if($cmd=="SORT"){
		$filter_category = get_param("filter_category");
		$filter_profit = get_param("filter_profit");
		$filter_price = get_param("filter_price");
		$filter_num_of_sellers = get_param("filter_num_of_sellers");
		$filter_amazon_prime = get_param("filter_amazon_prime");
		$where_sql = "";
		$where_sql .= $filter_category!="" ? " and category like '%$filter_category%'" : "";
		$where_sql .= $filter_profit!="" ? " and expected_profit => $filter_profit" : "";
		$where_sql .= $filter_price!="" ? " and price_jp <= $filter_price" : "";
		$where_sql .= $filter_num_of_sellers!="" ? " and num_of_sellers <= $filter_num_of_sellers" : "";
		$where_sql .= $filter_amazon_prime!="" ? " and amazon_prime = 1" : "";
	}
?>
