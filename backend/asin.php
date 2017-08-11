<?php
	require_once("db.php");
	//
	$cmd = get_param('cmd');
	//
	if($cmd=="ADD"){
		$url = get_param("url");
		$link = connect();
		$values = ["url" => $url, "user_id"=>$_SESSION['user_id']];
		insert(TABLE_ASIN_URLS,$values,$link);
		//header("Location: ../asin.php?add_url_success=1");
	}
	else if($cmd=="START"){
		$link = connect();
		$ids = get_param("ids");
		foreach ($ids as $id) {
			$values = ["status" => 2, "user_id"=>$_SESSION['user_id']];
			update(TABLE_ASIN_URLS,$values," WHERE id=$id",$link);
		}
		exec("nohup php -qf /var/www/html/amazon-tool/service/asin.php >/dev/null 2>&1 &");
		//header("Location: ../asin.php?start_success=1");
	}
	else if($cmd=="SUSPEND"){
		$link = connect();
		$ids = get_param("ids");
		foreach ($ids as $id) {
			$values = ["status" => 0, "user_id"=>$_SESSION['user_id']];
			update(TABLE_ASIN_URLS,$values," WHERE id=$id",$link);
		}
		//header("Location: ../asin.php?suspend_success=1");
	}
	else if($cmd=="DELETE"){
		$link = connect();
		$ids = get_param("ids");
		$delete_sql = "";
		foreach ($ids as $id) {
			$delete_sql .= ",".$id;
		}
		if($delete_sql!=""){
			$delete_sql = substr($delete_sql, 1);
			delete(TABLE_ASIN_URLS," WHERE id in ($delete_sql) and user_id=".$_SESSION['user_id'],$link);
			delete(TABLE_ASINS," WHERE asin_url_id in ($delete_sql) and user_id=".$_SESSION['user_id'],$link);
		}
		//header("Location: ../asin.php?suspend_success=1");
	}
	else if($cmd=="DL"){
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data.csv');
		$output = fopen('php://output', 'w');
		$link = connect();
		$asin_url_id = get_param("asin_url_id");
		if($asin_url_id!=""){
			$res = query(TABLE_ASINS,[]," WHERE asin_url_id=$asin_url_id and user_id=".$_SESSION['user_id'],$link);
			while($row = mysqli_fetch_array($res)){
				fputcsv($output, array($row['asin']));
			}
		}
		fclose($output);
		exit;
	}
?>