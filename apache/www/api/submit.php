<?php

header('Content-Type: application/json');
include_once("/var/www/html/private/env.php");

if(empty($_POST)) $_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['pid']) and is_numeric(($_POST['pid']))){
	$pid = $_POST['pid'];
} else respond(400, "Parameter missing `pid`.");

if(isset($_POST['code']) and isset($_POST['language'])){
	$code = $_POST['code'];
	$language = $_POST['language'];
} else respond(400, "Parameter missing `code` or `language`.");

if($language != "C" and $language != "CPP" and $language != "PYTHON2" and $language != "PYTHON3" and $language != "JAVA") respond(400);

if(!($user = verify())) respond(401);

$uid = $user['ID'];
$hash = hash("md5", $code);
$code = preg_replace('~\r\n?~', "\n", $code);
$now = date('Y-m-d H:i:s');

$sql = MySQL();

$queryProblem = $sql->prepare("SELECT `ID` FROM Problems WHERE `ID` = ? and isPublic = 1");
$queryProblem->bind_param('d', $pid);
$queryProblem->execute();

if($queryProblem->errno) respond(500, $queryProblem->error);
$queryProblem->store_result();
if($queryProblem->num_rows == 0) respond(404);

$querySolution = $sql->prepare("SELECT `ID` FROM Solutions WHERE Submitter = ? and hash = ? and Language = ?");
$querySolution->bind_param('dss', $uid, $hash, $language);
$querySolution->execute();
if($querySolution->errno) respond(500, $querySolution->error);
$querySolution->store_result();
if($querySolution->num_rows) respond(403, "重複上傳");

$updatePStatic = $sql->prepare("UPDATE Accounts SET Submit_Times = Submit_Times + 1 WHERE ID = ?;");
$updatePStatic->bind_param('d', $pid);
$updatePStatic->execute();

$updateUStatic = $sql->prepare("UPDATE Accounts SET Submit_Times = Submit_Times + 1 WHERE ID = ?;");
$updateUStatic->bind_param('d', $uid);
$updateUStatic->execute();

$addSolution = $sql->prepare("INSERT INTO Solutions (`Language`, `ProblemID`, `Hash`, `Submitter`, `Upload_Time`) VALUES (?,?,?,?,?) ");

$addSolution->bind_param('sssds', $language, $pid, $hash, $uid, $now);
$addSolution->execute();
if($addSolution->errno) respond(500, $addSolution->error);

$sid = $addSolution->insert_id;
$solution = fopen("/var/www/submissions/$sid", "w");
fwrite($solution, $code);
fclose($solution);

respond(200);

?>