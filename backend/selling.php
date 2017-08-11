<?php
	require_once("db.php");
	//
	$cmd = get_param('cmd');
	$where_sql = "";
	//
	if($cmd=="EXECUTE ACTION"){
		$link = connect();
		// DELETE
		$delete_asins = get_param("delete");
		if(isset($delete_asins) && $delete_asins!=""){
			$delete_sql = "";
			foreach ($delete_asins as $delete_asin) {
				$delete_sql .= ",'".trim($delete_asin)."'";
			}
			if($delete_sql!=""){
				$delete_sql = substr($delete_sql, 1);
				delete(TABLE_PRODUCTS," WHERE asin in ($delete_sql) and user_id=".$_SESSION['user_id'],$link);
			}
		}
		// BL
		$bl_asins = get_param("bl");
		if(isset($bl_asins) && $bl_asins!=""){
			$bl_sql = "";
			foreach ($bl_asins as $bl_asin) {
				$bl_sql .= ",'".trim($bl_asin)."'";
			}
			if($bl_sql!=""){
				$bl_sql = substr($bl_sql, 1);
				$values = ["status" => STATUS_BL,"user_id"=>$_SESSION['user_id']];
				update(TABLE_PRODUCTS,$values," WHERE asin in ($bl_sql)",$link);
			}
		}
		//header("Location: ../extraction.php?action_success=1");
	}
	else if($cmd=="HOURLY UPDATE"){
		$link = connect();
		//
		$hourly_update = get_param("hourly_update","0");
		$values = ["meta_name"=>"hourly_update", "meta_value" => $hourly_update, "user_id"=>$_SESSION['user_id']];
		replace(TABLE_OPTIONS,$values,$link);
	}
	$filter_amazon_prime = get_param("filter_amazon_prime",empty($_POST) ? $_SESSION['amazon_prime'] : "");
	$_SESSION['amazon_prime'] = $filter_amazon_prime;
?>
