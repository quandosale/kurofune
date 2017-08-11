<?php
	require_once("db.php");
	require_once("Client.php");
	//
	$cmd = get_param('cmd');
	//
	if($cmd=="EXECUTE ACTION"){
		$link = connect();
		$delete_order_ids = get_param("delete");
		// DELETE
		if(isset($delete_order_ids) && $delete_order_ids!=""){
			$delete_sql = "";
			foreach ($delete_order_ids as $delete_order_id) {
				$delete_sql .= ",'".trim($delete_order_id)."'";
			}
			if($delete_sql!=""){
				$delete_sql = substr($delete_sql, 1);
				delete(TABLE_PURCHASE," WHERE order_id in ($delete_sql) and user_id=".$_SESSION['user_id'],$link);
			}
		}
	}
	else if($cmd=="SET"){
		$field = get_param("field");
		$value = get_param("value");
		$order_id = get_param("order_id");
		$values = [$field => $value, "user_id"=>$_SESSION['user_id']];
		update(TABLE_PURCHASE,$values," WHERE order_id = '$order_id'",$link);
	}
?>