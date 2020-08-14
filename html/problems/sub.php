<?php

if(!isset($_GET['id'])){
	header("location: /");
	exit();
}
$pid = $_GET['id'];
if(!is_numeric($pid)){
	header("location: /405");
	exit();
}

include("/var/www/interfaces/mysql.php");
include('/var/www/html/account_process/validate.php');

$code = $_POST['code'];
$language = $_POST['language'];
setcookie("language", $language, time() + 604800);

if(!($language == "PYTHON2" OR $language =="PYTHON3" OR $language =="CPP" OR $language == "C" OR $language  == "JAVA")){
	header("location: /404");
	exit();
}
if(validate() == -1){
	setcookie("temp_code", $code, time() + 604800 , "/");
	die('<script>alert("登入失效，請重新登錄！");history.go(-1);</script>');
}

$hash = hash("md5", $code);
$sql = new MySQL();
$result = $sql -> select("Problems", "ID = " . $pid);
if($result -> num_rows == 0){
	header("location: /404");
	exit();
}
$Submit_times = $result -> fetch_array()['Submit_Times'];

$result = $sql -> select("Solutions", "Hash = '$hash' AND Language='$language' AND ProblemID = $pid AND Submitter = ". validate());
if($result -> num_rows != 0){
	die('<script>alert("請勿重複提交相同的代碼！");history.go(-1);</script>');
}

$sql -> insert("Solutions", array("Language", "ProblemID", "Hash", "Submitter", "Upload_Time"), array($language, $pid, $hash, validate(), date('Y-m-d H:i:s')));

$result = $sql -> select("Solutions", "Hash = '$hash' AND Language='$language'  AND ProblemID = $pid AND Submitter = ". validate());
if($result -> num_rows == 0){
	header("location: /500");
	exit();
} $result = $result -> fetch_array();
$code = preg_replace('~\r\n?~', "\n", $code);
$solution = fopen("/var/www/judger/solutions/" . strval($result['ID']), "w");

fwrite($solution, $code);
fclose($solution);

$result2 = $sql -> select("Accounts", "ID = " . validate());
$Submit_times2 = $result2-> fetch_array()['Submit_Times'];
$sql -> update("Problems", array("Submit_Times"), array($Submit_times + 1), "ID = " . $_GET['id']);
$sql -> update("Accounts", array("Submit_Times"), array($Submit_times2 + 1), "ID = " . validate());
chmod("/var/www/judger/solutions/" . strval($result['ID']), 775);
header("location: /status");
exit();

?>