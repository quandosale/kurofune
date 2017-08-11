<?php 
$retJson= array();
require_once("backend/db.php");
if(isset($_POST['data']) && $_POST['data']=='suspendRecord'){
	$res =	mysqli_query($link, " update users set status='".@$_POST['actionType']."' where id ='".@$_POST['id']."'" ) or die(mysqli_error());
	$retJson['service_no']   = @$res;
}else if(isset($_POST['data'] )  && $_POST['data']=='delRecord'){
	$res =	mysqli_query($link, " Delete from users where id ='".@$_POST['id']."'" ) or die(mysqli_error());
	$retJson['service_no']   = @$res;
}else{

	foreach (@$_POST['registration_date'] as $key => $value) {
		# code...
		$res =	mysqli_query($link, "insert INTO users (id, name, password, email, status, created_at) VALUES ('".@$_POST['id'][$key]."', '".@$_POST['name'][$key]."', '" .@$_POST['password'][$key]."', '" .@$_POST['email'][$key]."', '1', '" .@$_POST['registration_date'][$key]."') on duplicate key update name= '".@$_POST['name'][$key]."' ,password= '".@$_POST['password'][$key]."' ,email= '".@$_POST['email'][$key]."' , created_at= '".date('Y-m-d', strtotime(@$_POST['registration_date'][$key]))." 00:00:00' " ) or die(mysqli_error());
	}
	$retJson['service_no']   = @$res;
}
header("Content-Type: application/json; charset=utf-8");
echo json_encode($retJson);
        ?>