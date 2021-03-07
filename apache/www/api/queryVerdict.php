<?php

header('Content-Type: application/json');
include_once("/var/www/html/private/env.php");

if(isset($_GET['sid']) and is_numeric($_GET['sid'])){
    $sid = $_GET['sid'];
} else respond(400);

$user = verify();
if($user == false) respond(401);

$sql = MySQL();
$query = $sql->prepare("SELECT * FROM Solutions WHERE ID = ?");
$query->bind_param('d', $sid);
$query->execute();
if($query->errno) respond(500, $query->error);
$result = $query->get_result();
if($result->num_rows == 0) respond(404);
$result = $result->fetch_assoc();

if(!($result['Submitter'] == $user['ID'] or $user['auth'] == 1)) respond(403);

$code = file_get_contents("/var/www/submissions/$sid");

if($result['Status'] < 2){
    $data = array(
        "status"=> $result['Status'],
        "time"=>-1,
        "memory"=>-1,
        "subproblems"=>array(),
        "language"=>$result['Language'],
        "code"=>$code,
        "pid"=>$result['ProblemID']
    );
    respond(200, "Ok!", $data);
}

$problem_data = json_decode(file_get_contents("/var/www/verdicts/$sid.json"), true);
$timecost = 0;
$memorycost = 0;

$subproblems = array();
for($i = 0; $i < count($problem_data['Subproblems']); $i ++){
	$subproblem = array();
	foreach($problem_data['Subproblems'][$i]['Testcases'] as $s){
		if($problem_data['Subproblems'][$i]['Debugger']){
			// 公開
			$subproblem[] = array(
				"status"=>$s['status'],
				"input"=>$s['Input'],
				"output"=>$s['Output'],
				"time"=>$s['Time'],
				"memory"=>$s['Memory'],
				"ans"=>$s['Answer'],
                "description"=>$s['Description'],
                "public"=>1
			);
		} else {
			// 不公開
			$subproblem[] = array(
				"status"=>$s['status'],
				"input"=>"none",
				"output"=>"none",
				"time"=>$s['Time'],
				"memory"=>$s['Memory'],
				"ans"=>"none",
                "description"=>"none",
                "public"=>0
			);
		}
	}
	$subproblems[] = array("index"=>$i+1, "testcases"=>$subproblem);
	$timecost = max($timecost, $s['Time']);
	$memorycost = max($memorycost, $s['Memory'] * 1024);
}

$data = array(
	"status"=> $result['Status'],
	"time"=>$timecost,
	"memory"=>$memorycost,
    "subproblems"=>$subproblems,
    "language"=>$result['Language'],
    "code"=>$code,
    "pid"=>$result['ProblemID']
);

if(isset($problem_data['Description'])) $data['description'] = $problem_data['Description'];
respond(200, "Ok!", $data);


?>