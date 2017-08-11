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
		if($username=="admin" && $password=="123456"){
			$_SESSION['admin'] = "1";
			header("Location: operator-users.php");
		}
		$error = "Invalid Login!";
	}
?>