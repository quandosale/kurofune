<?php
	require_once("db.php");
	//
	$cmd = get_param('cmd');
	$where_sql = "";
	//
	if($cmd=="IMPORT"){
		$link = connect();
		$csv_file = $_FILES["csv_file"]["tmp_name"];
		if($csv_file!=""){
			$csv_rows = file($csv_file,FILE_IGNORE_NEW_LINES);
			foreach ($csv_rows as $asin) {
				$values = ["asin" => $asin, "user_id"=>$_SESSION['user_id']];
				insert(TABLE_BLACKLIST,$values,$link);
			}
			exec("nohup php -qf /var/www/html/amazon-tool/service/blacklist.php >/dev/null 2>&1 &");
		}
	}
	else if($cmd=="EXECUTE ACTION"){
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
				delete(TABLE_BLACKLIST," WHERE asin in ($delete_sql) and user_id=".$_SESSION['user_id'],$link);
			}
		}
	}
	else if($cmd=="ADD"){
		$link = connect();
		$keyword = get_param("keyword");
		$values = ["keyword" => $keyword, "user_id"=>$_SESSION['user_id']];
		insert(TABLE_BLACKLIST_KEYWORDS,$values,$link);
		//header("Location: ../asin.php?add_url_success=1");
	}
	else if($cmd=="DELETE"){
		$link = connect();
		$delete_keywords = get_param("delete");
		if(isset($delete_keywords) && $delete_keywords!=""){
			$delete_sql = "";
			foreach ($delete_keywords as $delete_keyword) {
				$delete_sql .= ",'".trim($delete_keyword)."'";
			}
			if($delete_sql!=""){
				$delete_sql = substr($delete_sql, 1);
				delete(TABLE_BLACKLIST_KEYWORDS," WHERE id in ($delete_sql) and user_id=".$_SESSION['user_id'],$link);
			}
		}
	}
?>