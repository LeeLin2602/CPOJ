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
	include('/var/www/html/account_process/validate.php');
	include('/var/www/interfaces/mysql.php');

	$sql = new MySQL();
	$result = $sql -> select("Problems", "ID = " . $pid);
	if($result -> num_rows == 0){
		header("location: /404");
		exit();
	}
	$result = $result -> fetch_array();

	if(validate() == -1){
		header("location: /login?back=/showProblem?id=".$result['ID']);
		exit();
	}

	$problem_data = json_decode(file_get_contents("/var/www/problems/$pid.json"), true);  
	$problem_data['Content'] = str_replace("\\n", "\n", ($problem_data['Content']));
	$problem_data['InputExplanation'] = str_replace("\\n", "\n", ($problem_data['InputExplanation']));
	$problem_data['OutputExplanation'] = str_replace("\\n", "\n", ($problem_data['OutputExplanation']));
	$problem_data['TestCasesInfo'] = str_replace("\\n", "\n", ($problem_data['TestCasesInfo']));
	
	include('/var/www/html/vendor/print_head.php');
	echo("<title>CPOJ 提交代碼 ".$result['ID'] . ". " .$result['Title']."</title>");
?>
<form action="submit?id=<?php echo($result['ID']); ?>" method="POST">
<a href="/showProblem?id=<?php echo($result['ID']);?>">回題目</a><br/><br/>


    <div class="row" style="">
      <div class="col-xs-1 " style="width:100%">

        <div class="card h-100"  style="width:100%">
          <h4 class="card-header" style="text-align: center;">題目要求</h4>
          <div class="card-body">
            <p class="card-text">
            	<div class = "container">
            	<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							輸入說明：
						</div>
						<div class="panel-body">
							<br/>
							<div id="problem_theinput" class="problembox"><?php echo($problem_data['InputExplanation']);?></div>
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
							<div id="problem_theoutput" class="problembox"><?php echo($problem_data['OutputExplanation']);?></div>
						</div>
					</div>
				</div>

                <script>
                	markjax($("#problem_theinput").html(), document.getElementById('problem_theinput'));
                	markjax($("#problem_theoutput").html(), document.getElementById('problem_theoutput'));
			    </script>
				</div><hr/><br/><div class="row">
					
<?php
	$problem_data = json_decode(file_get_contents("/var/www/problems/$pid.json"), true);  

	$ex = $problem_data['IOExample'][0];
	echo('				<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						輸入範例：
					</div>
					<div class="panel-body">
						<br/>
						<div id="problem_theinput0" class="problembox">' . $ex[0] .' </div><br/>
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
							<div id="problem_theoutput0" class="problembox">' . $ex[1] . '</div><br/>
					</div>
				</div>
			</div>
			');
?>

                <script>
                	markjax($("#problem_theinput0").html(), document.getElementById('problem_theinput0'));
                	markjax($("#problem_theoutput0").html(), document.getElementById('problem_theoutput0'));
			    </script>
				</div>
		      </div>
            </p>
          </div>
        </div>
    </div>
</div>
<br/>

	<br/>
<?php if(isset($_GET['status'])){echo('<span style="color:red">'. $_GET['status'] .'</span>');}?>
<textarea id="code" name="code" rows="10" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php if(isset($_COOKIE['temp_code'])){setcookie("temp_code", "", time() , "/");echo($_COOKIE['temp_code']);} ?></textarea><br/>
<div id="Compilers" style="text-align: left;height:8em;">
	<div style="display: block; clear: both;">
		解題語言：<br>
		
			<input name="language" id="C" type="radio" value="C" userlanguage="">
			<span style="font-weight: bold; font-size: large">C</span>: gcc -std=c11(gcc 7.4.0)<br>
		
			<input name="language". id="CPP" type="radio" value="CPP" userlanguage="C">
			<span style="font-weight: bold; font-size: large">CPP</span>: g++ -std=c++14(g++ 7.4.0)<br>
			<input name="language" id="JAVA" type="radio" value="JAVA" userlanguage="C">
			<span style="font-weight: bold; font-size: large">JAVA</span>: OpenJDK java version 11.0.7 （class名稱必須為"P<?php echo($_GET['id'])?>"）<br>
			<!-- Fuck Java, I debugged this for fucking THREE DAYS! -->
			<input name="language" id="PYTHON2" type="radio" value="PYTHON2" userlanguage="C">
			<span style="font-weight: bold; font-size: large">PYTHON2</span>: Python 2.7.17<br>

			<input name="language" id="PYTHON3" type="radio" value="PYTHON3" userlanguage="C">
			<span style="font-weight: bold; font-size: large">PYTHON3</span>: Python 3.7.5<br>
			<script>
				document.getElementById("<?php
					if(isset($_COOKIE['language'])){echo($_COOKIE['language']);}else{echo("C");}
				?>").checked = true;
			</script>
	</div></div><br/>
<div style="text-align: center;"><button class="btn btn-primary" id="sendMessageButton" type="submit">提交</button></div>
</form>

<?php include('/var/www/html/vendor/print_foot.php');?>