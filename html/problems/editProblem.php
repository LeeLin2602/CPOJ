<?php

include("/var/www/interfaces/mysql.php");
include('/var/www/html/account_process/validate.php');
include("/var/www/html/vendor/print_head.php");

$sql = new MySQL();

$pid = -1;
$title = ""; $problem = ""; $input = ""; $output = ""; $testcases = ""; $tags = ""; $source = "";$input2 = ""; $output2 = "";$difficulty = 0; $testdatas = '{"Subproblems": [{"Score" : 100,"Debugger": true,"Testcases":[["0\n0", "0", "2000", "512"]]}]}';
if(isset($_GET['id']) and is_numeric($_GET['id'])){
	$pid = intval($_GET['id']);
	$problem_data = json_decode(file_get_contents("/var/www/problems/$pid.json"), true);  
	$result = $sql -> select("Problems", "ID = " . $pid);
	if($result -> num_rows == 0){
		header("location: /500");
		exit();
	}
	$result = $result -> fetch_array();
	$title = $result['Title'];
	$source = $result['Source'];
	$difficulty = $result['Difficulty'];
	if(count($problem_data['IOExample']) > 0){
		$input2 = $problem_data['IOExample'][0][0]; $output2 = $problem_data['IOExample'][0][1];
	}
	$problem_data = json_decode(file_get_contents("/var/www/problems/$pid.json"), true);  
	$problem = str_replace("\\n", "\n", ($problem_data['Content']));
	$input = str_replace("\\n", "\n", ($problem_data['InputExplanation']));
	$output = str_replace("\\n", "\n", ($problem_data['OutputExplanation']));
	$testcases = str_replace("\\n", "\n", ($problem_data['TestCasesInfo']));
	$testdatas = '{"Subproblems":' . str_replace("\n", "\\n", json_encode(($problem_data['Subproblems']))) . "}";
}


$result = $sql -> select("Accounts", "ID = " . strval(validate()). " and auth = 1");
if($result -> num_rows == 0){
	header("location: /404");
	exit();
}
$tags = "";
if($pid != -1){
	$result = $sql -> select("Tags", "ProblemID = $pid");
	while($row = $result -> fetch_array()){
		$row = $sql -> select("TagsName", "ID = " . $row["KeyID"]) -> fetch_array();
		if($tags != ""){$tags .= ", " . $row['KeyName'];}
		if($tags == ""){$tags =  $row['KeyName'];}
	}
}
?>
<title>CPOJ 編輯題目</title>
<div>
<form action="edit?id=<?php echo($pid); ?>" method="POST">
標題：<br/><input type="text" class="form-control" name="title" id="title" title="title" required data-validation-required-message="請輸入標題" value="<?php echo($title); ?>" /><br/>
<div class="row">
	<div class="col-md-6">
	難度：
	<input type="text" class="form-control" name="difficulty" id="difficulty" title="difficulty" data-validation-required-message="請輸入難度" value="<?php echo($difficulty); ?>" />
	0, 1, 2 分別對應簡單、中等、困難
	</div>
	<div class="col-md-6">
	來源：
	<input type="text" class="form-control" name="source" id="source" title="source" data-validation-required-message="請輸入標題" value="<?php echo($source); ?>" />
	</div>
</div>
<div>
標籤：
<input type="text" class="form-control" name="tags" id="tags" title="tags" data-validation-required-message="請輸入標題" value="<?php echo($tags); ?>" />
</div>
<br/><br/>
問題敘述（markdown）：
<textarea id="problem" name="problem" rows="10" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php echo($problem); ?></textarea><br/><br/>
<div class="row">
	<div class="col-md-6">
	輸入說明：
	<textarea id="problem" name="input" rows="3" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php echo($input); ?></textarea><br/>
	</div><div class="col-md-6">
	輸出說明：
	<textarea id="problem" name="output" rows="3" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php echo($output); ?></textarea><br/>
	</div>
</div><div class="row">
	<div class="col-md-6">
	輸入範例：
	<textarea id="problem" name="input2" rows="3" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php echo($input2); ?></textarea><br/>
	</div><div class="col-md-6">
	輸出範例：
	<textarea id="problem" name="output2" rows="3" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php echo($output2); ?></textarea><br/>
	</div>
</div>
測資說明：
<textarea id="problem" name="testcases" rows="3" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php echo($testcases); ?></textarea><br/>
輸入測資：
<textarea id="testdatas" name="testdatas" rows="3" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php echo($testdatas); ?></textarea><br/><br/>
<button class="btn btn-primary" id="sendMessageButton" type="submit">上傳</button>
</form>
</div>
<?php 
include("/var/www/html/vendor/print_foot.php");
?>