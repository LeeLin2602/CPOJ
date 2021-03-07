<?php

header('Content-Type: application/json');
include_once("/var/www/html/private/env.php");

if(isset($_GET['pid']) and is_numeric($_GET['pid'])){
    $pid = $_GET['pid'];
} else respond(400);

$sql = MySQL();
$query = $sql->prepare("
SELECT B.`KeyName` FROM Tags A
LEFT JOIN TagsName B ON A.`KeyID` = B.`ID`
WHERE A.`ProblemID` = ?");
$query->bind_param("d", $pid);
$query->execute();
if($query->errno) respond(500, $msg = $query->error);
$result = $query->get_result();
$res = array();
while($row = $result->fetch_assoc()){
    $res[] = $row["KeyName"];
}
respond(200, "Ok!", $res);
?>
