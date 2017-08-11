<?php
	require_once("db.php");
	//
	$cmd = get_param('cmd');
	$error = "";
	//
	if($cmd=="login"){
		session_start();
		$username = get_param("username");
		$password = get_param("password");
		$user_id = check_login($username,$password);
		if($user_id > 0){
			$_SESSION['user'] = $username;
			$_SESSION['user_id'] = $user_id;
			header("Location: asin.php");
		}
		$error = "Invalid Login!";
	}
	function check_login($username,$password){
		if($username!="pp-furzealga" && $username!="pp-boarwily" && $username!="pp-youlaugh")
			return -1;
		$link = connect();
		$res = query(TABLE_USERS,[]," WHERE name='$username' and password = ENCRYPT('$password', password);",$link);
    	$row = mysqli_fetch_array($res);
		print_r($row);
    	return $row ? $row['id'] : -1; 
	}
?>