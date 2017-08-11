<?php
	require_once("backend/db.php");
	//
	if(get_param("username")==""){
		echo crypt("QItAxzRYZb2w.");
		exit;
	}
	$result = [];
	$result['name'] = get_param("username");
	$result['password'] = get_param("password");
	$result['email'] = get_param("payer_email");
	$result['subscr_id'] = get_param("subscr_id");
	$result['txn_type'] = get_param("txn_type");
	//
	$link = connect();
	insert(TABLE_USERS,$result,$link);
	file_put_contents("ipn.txt", file_get_contents("ipn.txt").implode(";", $_REQUEST)."\n");
?>