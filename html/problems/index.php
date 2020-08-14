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
	include('/var/www/interfaces/mysql.php');
	include('/var/www/html/account_process/validate.php');
	$sql = new MySQL();
	$auth = $sql -> select("Accounts", "auth = 1 and ID = " . strval(validate())) -> num_rows != 0;

	$result = $sql -> select("Problems", "ID = " . $pid);
	if($result -> num_rows == 0){
		header("location: /404");
		exit();
	}
	
	$result = $result -> fetch_array();

	if($result['isPublic'] == 0 AND !$auth) {
		header("location: /404");
		exit();
	}

	$Tags = $sql -> select("Tags", "ProblemID = " . $pid);
	$tags = "";
	while($Tag = $Tags -> fetch_array()){
		$Tag = $sql -> select("TagsName", "ID = " . $Tag["KeyID"]) -> fetch_array()['KeyName'];
		if($tags == ""){
			$tags = $Tag;
		} else {
			$tags .= ", " . $Tag;
		}
	}
	$problem_data = json_decode(file_get_contents("/var/www/problems/$pid.json"), true);  
	
	include('/var/www/html/vendor/print_head.php');
	echo("<title>CPOJ ".$result['ID'] . ". " .$result['Title']."</title>");
	echo('<meta name="Title" content="' . $result['ID'] . ". " .$result['Title'] . '" />');
	echo('<meta name="Description" content="' . $tags . " " .$result['Source'] . '" />');

?>
      	<div class="row">
			<div class="col-md-2">
					<a href="/listProblems">回題庫</a>&emsp;
					<?php
						if($auth){
							echo('<a href="/editProblem?id=' .$result['ID']  .'">修改題目</a>');
						}
					?>
			</div>
			<div class="col-md-6 text-center">
				<div class="h1">
					<br/><?php echo($result['ID'] . ". " .$result['Title']);?><br/><br/>
				</div>
			</div>
			<div class="col-md-4">
				<?php 
					if($tags != ""){ echo("標籤：" . $tags. "<br/>");}
					if($result['Source'] != ""){echo("來源：" . $result['Source']. "<br/>");}
					if($result['Submit_Times'] != 0){echo("通過率：" . $result['AC_Times']. "/" . $result['Submit_Times']. " <br/>");}
					echo("難度：" . array("入門","簡單","中等","進階", "困難")[$result['Difficulty']] . "<br/>");
				?>
				<br/>
			</div>
		</div>


    <div class="row" style="">
      <div class="col-xs-1 " style="width:100%">

        <div class="card h-100"  style="width:100%">
          <h4 class="card-header" style="text-align: center;">題目敘述</h4>
          <div class="card-body">
            	<div id="Problem" class="container"><?php echo(($problem_data['Content'])); ?></div>
            	<br/><br/><br/><hr/><br/>
            	<div class = "container">
            	<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							輸入說明：
						</div>
						<div class="panel-body">
							<br/>
							<div id="problem_theinput" class="problembox"><?php echo(($problem_data['InputExplanation']));?>
						</div>
						</div>
					</div>
					</div>
					<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							輸出說明：
						</div>
						<div class="panel-body">
							<br/>
								<div id="problem_theoutput" class="problembox"><?php echo(($problem_data['OutputExplanation']));?>
							</div>
							</div>
						</div>
					</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
					<br/>
					<br/>
							測資限制：
						</div>
						<div class="panel-body">
							<br/>
								<div id="problem_testcase" class="problembox"><?php echo(($problem_data['TestCasesInfo']));?></div><br/><br/>
						</div>
					</div>
				</div><hr/></div>
			</div>

                <script>
                	markjax($("#Problem").html(), document.getElementById('Problem'));
                	markjax($("#problem_theinput").html(), document.getElementById('problem_theinput'));
                	markjax($("#problem_theoutput").html(), document.getElementById('problem_theoutput'));
                	markjax(document.getElementById("problem_testcase").innerHTML, document.getElementById('problem_testcase'));
			    </script>

				<div class="container"><div class="row">
<?php
	$c = 0;
	foreach($problem_data['IOExample'] as $ex){
		$c++;
		echo('				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							輸入範例：
						</div>
						<div class="panel-body">
								<br/>
							<div id="problem_theinput' . $c .'" class="problembox">' . $ex[0] .' </div><br/><br/>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							輸出範例：
						</div>
						<div class="panel-body">
								<br/>
								<div id="problem_theoutput' . $c . '" class="problembox"> ' . $ex[1] . '</div><br/><br/>
						</div>
					</div>
				</div>

				    <script>
                	markjax($("#problem_theinput' . $c .'").html(), document.getElementById("problem_theinput' . $c .'"));
                	markjax($("#problem_theoutput' . $c .'").html(), document.getElementById("problem_theoutput' . $c .'"));
			    </script>
				');
	}
?>
				</div>
		      </div>
            </p>
          </div>
        </div>
      </div>
    </div>
    <br/ >
</div>

	<div class = "row">		
	<div class = "col">		
		<a href="/submitSolution?id=<?php echo($result['ID']);?>" class="btn btn-primary">提交代碼</a>&emsp;
		<a href="/status?pid=<?php echo($result['ID']);?>" class="btn btn-primary">本題狀態</a>&emsp;
		<a href="" class="btn btn-primary">本題討論</a><br/><br/>
	</div>

<?php include('/var/www/html/vendor/print_foot.php');?>