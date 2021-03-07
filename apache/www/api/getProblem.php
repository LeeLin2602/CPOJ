<?php

header('Content-Type: application/json');
include_once("/var/www/html/private/env.php");

if(isset($_GET['pid']) and is_numeric($_GET['pid'])){
    $pid = $_GET['pid'];
} else respond(400);

$sql = MySQL();
$stmt = $sql->prepare("SELECT `ID`, `Title`, `Source`, `Difficulty`, `Submit_Times`, `AC_times` FROM Problems WHERE `ID` = ? AND `isPublic` = 1");
$stmt->bind_param("d", $pid);
$stmt->execute();
if($stmt->errno) respond(500, $stmt->error);
$result = $stmt->get_result();
if($result->num_rows == 0) respond(404);
$result = $result->fetch_assoc();

$problem_data = json_decode(file_get_contents("/var/www/problems/$pid.json"), true);
$problem_data = array(
    "Content"=>$problem_data["Content"],
    "InputExplanation"=>$problem_data["InputExplanation"],
    "OutputExplanation"=>$problem_data["OutputExplanation"],
    "TestCasesInfo"=>$problem_data["TestCasesInfo"],
    "IOExample"=>$problem_data['IOExample']
);
$result['Problem'] = $problem_data;
respond(200, $msg = "Ok!", $data = $result);
?>
