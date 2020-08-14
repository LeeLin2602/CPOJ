<?php

include("/var/www/interfaces/mysql.php");
include('/var/www/html/account_process/validate.php');

$sql = new MySQL();
$result = $sql -> select("Accounts", "ID = " . strval(validate()). " and auth = 1");
if($result -> num_rows == 0){
	header("location: /404");
	exit();
}

$pid = -1;
$title = ""; $problem = ""; $input = ""; $output = "";$tags = ""; $source = ""; $input2 = ""; $output2 = "";$testdatas = array();$difficulty = 0;

if(isset($_GET['id']) and is_numeric($_GET['id']) and $_GET['id'] != "-1"){
	$pid = intval($_GET['id']);
	$result = $sql -> select("Problems", "ID = " . $pid);
	if($result -> num_rows == 0){
		header("location: /500");
		exit();
	}
}

if(isset($_POST['title'])){
	$title = htmlspecialchars(str_replace(";","；",$_POST['title']),  ENT_QUOTES);
}
if(isset($_POST['tags'])){
	$tags = preg_split("/,/", htmlspecialchars(str_replace(";","；",$_POST['tags']),  ENT_QUOTES));
}
if(isset($_POST['source'])){
	$source = htmlspecialchars(str_replace(";","；",$_POST['source']),  ENT_QUOTES);
}
if(isset($_POST['problem'])){
	$problem = ($_POST['problem']);
}
if(isset($_POST['difficulty']) and is_numeric($_POST['difficulty'])){
	$difficulty = intval($_POST['difficulty']);
}
if(isset($_POST['testdatas'])){
	$testdatas = json_decode($_POST['testdatas']);
	$testdatas = $testdatas -> Subproblems;
}
if(isset($_POST['input'])){
	$input = ($_POST['input']);
}
if(isset($_POST['output'])){
	$output = ($_POST['output']);
}
if(isset($_POST['input2'])){
	$input2 = ($_POST['input2']);
}
if(isset($_POST['output2'])){
	$output2 = ($_POST['output2']);
}
if(isset($_POST['testcases'])){
	$testcases = ($_POST['testcases']);
}

if($pid != -1){
	$problem_data = json_decode(file_get_contents("/var/www/problems/$pid.json"), true);  
	$problem_data['Content'] = $problem;
	$problem_data['InputExplanation'] = $input;
	$problem_data['OutputExplanation'] = $output;
	$problem_data['IOExample'][0] = array($input2, $output2);
	$problem_data['TestCasesInfo'] = $testcases;
	$problem_data['Subproblems'] = $testdatas;
	$problem_file = fopen("/var/www/problems/$pid.json", "w");
	fwrite($problem_file, json_encode($problem_data));
	fclose($problem_file);
	$sql -> update("Problems", array("Title", "Source", "Difficulty"), array($title, $source, $difficulty), "ID = $pid");
	$sql -> delete("Tags", "ProblemID = $pid");
	foreach ($tags as $tag) {
		$tag = trim($tag);
		$result = $sql -> select("TagsName", "KeyName = '$tag'");
		if($result -> num_rows == 0){
			$sql -> insert("TagsName", array("KeyName"), array($tag));
			$result = $sql -> select("TagsName", "KeyName = '$tag'");
		} $result = $result -> fetch_array()["ID"];
		$sql -> insert("Tags", array("KeyID", "ProblemID"), array($result, $pid));
	}
} else {
	$sql -> insert("Problems", array("Title", "Source", "Difficulty"), array($title, $source, $difficulty));
	$id_result = $sql -> select("Problems", "Title = '$title' AND Source = '$source'", "", 1);
	$pid = ($id_result -> fetch_array())['ID'];
	$problem_data['Content'] = $problem;
	$problem_data['InputExplanation'] = $input;
	$problem_data['OutputExplanation'] = $output;
	$problem_data['TestCasesInfo'] = $testcases;
	$problem_data['IOExample'] = array(array($input2, $output2));
	$problem_data['Subproblems'] = $testdatas;
	$problem_file = fopen("/var/www/problems/$pid.json", "w");
	fwrite($problem_file, json_encode($problem_data));
	fclose($problem_file);
	$sql -> delete("Tags", "ProblemID = $pid");
	foreach ($tags as $tag) {
		$tag = trim($tag);
		$result = $sql -> select("TagsName", "KeyName = '$tag'");
		if($result -> num_rows == 0){
			$sql -> insert("TagsName", array("KeyName"), array($tag));
			$result = $sql -> select("TagsName", "KeyName = '$tag'");
		} $result = $result -> fetch_array()["ID"];
		$sql -> insert("Tags", array("KeyID", "ProblemID"), array($result, $pid));
	}
}

header("location: /showProblem?id=$pid");
exit();
?>