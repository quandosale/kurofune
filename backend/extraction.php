<?php
	require_once("db.php");
	require_once("Client.php");
	//
	$cmd = get_param('cmd');
	$where_sql = "";
	$import_file_name = "";
	//
	if($cmd=="IMPORT"){
		$link = connect();
		$csv_file = $_FILES["csv_file"]["tmp_name"];
		$import_file_name = $_FILES["csv_file"]["name"];
		if($csv_file!=""){
			$csv_rows = file($csv_file,FILE_IGNORE_NEW_LINES);
			// print_r($csv_rows);exit;
			foreach ($csv_rows as $asin) {
				$values = ["asin" => $asin, "user_id"=>$_SESSION['user_id']];
				insert(TABLE_PRODUCTS,$values,$link);
			}
			exec("nohup php -qf /var/www/html/amazon-tool/service/extraction_manager.php ".STATUS_NEW." >/dev/null 2>&1 &");
		}
	}
	else if($cmd=="IMPORT2"){
		$link = connect();
		$asin_import = get_param("asin_import");
		if($asin_import!=""){
			$values = ["asin" => $asin_import, "user_id"=>$_SESSION['user_id']];
			insert(TABLE_PRODUCTS,$values,$link);
			exec("nohup php -qf /var/www/html/amazon-tool/service/extraction_manager.php ".STATUS_NEW." >/dev/null 2>&1 &");
		}
	}
	else if($cmd=="DELETE ALL"){
		$link = connect();
		delete(TABLE_PRODUCTS," WHERE (status=".STATUS_DEFAULT." or status=".STATUS_NEW.") and user_id=".$_SESSION['user_id'],$link);
	}
	else if($cmd=="EXECUTE ACTION"){
		$link = connect();
		$delete_asins = get_param("delete");
		// DELETE
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
		// SELLING
		$start_selling_asins = get_param("start_selling");
		if(isset($start_selling_asins) && $start_selling_asins!=""){
			$user_settings = get_shipping_rates();
			foreach ($start_selling_asins as $start_selling_asin) {
				$start_selling_asin = trim($start_selling_asin);
				//
				// Price
				$res = query(TABLE_PRODUCTS,[]," WHERE asin='".$start_selling_asin."'",$link);
				$row = mysqli_fetch_array($res);
				$sell_price = doubleval($row['price_us']);
				$lowest_price = $sell_price;
				if($user_settings['minus_plus']=="minus")
					$lowest_price -= doubleval($user_settings['lowest_price']);
				else if($user_settings['minus_plus']=="plus")
					$lowest_price += doubleval($user_settings['lowest_price']);
				$final_sell_price = $user_settings['based_on_type']=="1" ? $lowest_price : $sell_price;
				// DB
				$dt = new Datetime();
				$selling_date = $dt->format('Y-m-d H:i:s');
				$values = ["status" => STATUS_SELLING, "selling_date" => $selling_date, "selling_price"=>$final_sell_price, "user_id"=>$_SESSION['user_id']];
				update(TABLE_PRODUCTS,$values," WHERE asin='$start_selling_asin'",$link);
				//
			}
			// Real Selling
			if(!empty($start_selling_asins))
				sell($start_selling_asins);
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
				$values = ["status" => STATUS_BL];
				update(TABLE_PRODUCTS,$values," WHERE asin in ($bl_sql) and user_id=".$_SESSION['user_id'],$link);
			}
		}
		//header("Location: ../extraction.php?action_success=1");
	}
	// else if($cmd=="SORT"){
		$filter_category = get_param("filter_category");
		$filter_profit = get_param("filter_profit");
		$filter_price = get_param("filter_price");
		$filter_num_of_sellers = get_param("filter_num_of_sellers");
		$filter_amazon_prime = get_param("filter_amazon_prime",empty($_POST) ? $_SESSION['amazon_prime'] : "");
		$_SESSION['amazon_prime'] = $filter_amazon_prime;
		$exclude_no_jp = get_param("exclude_no_jp",empty($_POST) ? $_SESSION['exclude_no_jp'] : "");
		$_SESSION['exclude_no_jp'] = $exclude_no_jp;
		$filter_page_count = get_param("filter_page_count",PAGE_COUNT);
		$where_sql = "";
		$where_sql .= $filter_category!="" ? " and LOWER(category) like '%".strtolower($filter_category)."%'" : "";
		$where_sql .= $filter_profit!="" ? " and ((".($filter_amazon_prime=='1' ? 'price_us_prime' : 'price_us')."*".US_JP_RATE.") - price_jp - (shipping) - (amazon_commission)) >= $filter_profit" : "";
		$where_sql .= $filter_price!="" ? " and price_jp <= $filter_price" : "";
		$where_sql .= $filter_num_of_sellers!="" ? " and num_of_sellers <= $filter_num_of_sellers" : "";
		$where_sql .= $exclude_no_jp=="1" ? " and price_jp > 0" : "";
		// $where_sql .= $filter_amazon_prime!="" ? " and amazon_prime = 1" : "";
	// }
?>
