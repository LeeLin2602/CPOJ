<?php

include("/var/www/interfaces/mysql.php");

$name = $_POST["name"];
$email = $_POST["email"];
$passwd = password_hash($_POST["password"], PASSWORD_ARGON2I);

if(!checkString($name, "/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{1,11}/u")){
  header('location: /register?status=用戶名格式錯誤');
  exit();
}
if(!checkString($email, "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/")){
  header('location: /register?status=電子郵箱格式錯誤');
  exit();
}

$sql = new MySQL();
$result = ($sql -> select("Accounts", "Email = '$email'")) -> num_rows;
if($result == 0){
	$sql -> insert("Accounts", array("Name", "Password", "Email", "Birthday"), array($name, $passwd, $email, date("Y/m/d")));
	header('location: /login');
} else {
	if($result != 0){
		header('location: /register?status=電子郵箱已被註冊');
	}
}

?>
