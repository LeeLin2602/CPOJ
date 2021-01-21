<?php
session_start();
include("/var/www/interfaces/mysql.php");

$email = $_POST["email"];
$passwd = $_POST["password"];




if(!checkString($email, "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/")){
	header("location: /login?status=帳號或密碼錯誤");
    exit();
}

$sql = new MySQL();
$result = $sql -> select("Accounts", "Email = '$email'");
if($result -> num_rows == 0){
	header("location: /login?status=帳號或密碼錯誤");
} else {
	$row = $result -> fetch_array();
	if(password_verify($passwd, $row['Password'])){
		$UID = hash("SHA512", $email . $row['Birthday'] . strval(time()));
		$_SESSION[$UID] = $row['ID'];
		$_SESSION[$UID . "Name"] = $row['Name'];
		setcookie("UID", $UID, time() + 604800 , "/");
		if(isset($_GET['back'])){
			echo("<script>window.location.href='".htmlspecialchars($_GET['back'])."';</script>");
			exit();
		}
		header("location: /");
		exit();
	} else {
		header("location: /login?status=帳號或密碼錯誤");
	}
}
?>