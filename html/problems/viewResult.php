<?php 
	if(isset($_GET['id']) and is_numeric($_GET['id'])){$id = intval($_GET['id']);} else {header("location: /404");exit();}
	
	include('/var/www/html/vendor/print_head.php');
	include('/var/www/interfaces/mysql.php');

	$sql = new MySQL();
	$result = $sql -> select("Solutions", "ID = " . $id);
	if(validate() == -1){
		header("location: /login?back=/viewResult?id=".$id);
		exit();
	}
	if($result -> num_rows == 0){
		header("location: /404");
		exit();
	}
	$solution = $result -> fetch_array();
	if($solution['0'] <= 1){
		header("location: /404");
		exit();
	}
	if(validate() != $solution['Submitter']){
		$result = $sql -> select("Accounts", "auth = 1 and ID = " . validate());
		if($result -> num_rows == 0){
			header("location: /403");
			exit();
		}
	}
	$problem_data = json_decode(file_get_contents("/var/www/problems/" . $solution[2] . ".json"), true);  
	$result = json_decode(file_get_contents("/var/www/judger/results/$id.json"), true); 
	$problem_inCompetition = $sql -> select("Problems", "ID = " . strval($solution[2])) -> fetch_array()["InCompetition"];



?>
<title>CPOJ 提交結果 <?php echo($id);?></title>
<h3>提交編號 <?php echo($id);?> ：<?php 
	echo(array("Pending", "Compiling", "AC", "PE", "TLE", "MLE", "WA", "RE", "OLE", "CE", "SE", "NA")[$solution['Status']]);
	if($solution['Status'] == 2){
		echo("(");
		$result['time'] = round($result['time'], 1);
		$result['memory'] = round($result['memory'], 2);
		if($result['time'] > 1000){
			echo($result['time'] / 1000); echo("s, ");
		} else {
			echo($result['time']); echo("ms, ");
		}

		if($result['memory'] > 1){
			echo($result['memory']); echo("mb");
		} else {
			echo($result['memory'] * 1024); echo("kb");
		}
		echo(")");

	}
	?></h3><br/>
<a href="/showProblem?id=<?php echo($solution['2']);?>">回題目</a><br/><br/>
<div class="container">
<table class="table" id="subProblemList">
	<thead><th style="text-align:center">子題編號</th><th>得分/占分</th><th>公開</th></thead>
	<tbody>
		<?php 
			$score = 0;
			for($i = 0; $i < count($result['Subproblems']); $i ++){
				echo('<tr><td style="text-align:center">' . strval($i + 1) . "</td>");
				if($result["status"] != 9){
					$getScore = $result['Subproblems'][$i]['GetScore'];
				}  else {$getScore = 0;}
				$score += $getScore;
				echo('<td>' . strval($getScore) . "/" . strval($result['Subproblems'][$i]['Score']) . '</td>');
				if($result['Subproblems'][$i]['Debugger'] AND !$problem_inCompetition){
					echo('<td>公開</td>');
				} else {
					echo('<td>非公開</td>');
				}
				echo("</tr>");
			}
			echo('<tr><td style="text-align:center">總得分：</td><td>' . $score. '</td><td></td></tr>');

		?>
	</tbody>
</table>
</div>
<br/><hr/>
<?php
	if($result['status'] == 9){
		echo('<br/><div class="container"><div class="container"><label>錯誤訊息：</label><br/>' . $result['Description'] . '</div></div>');
	}
?>
<br/>
<div class="container"><div class="container">
<?php 
	if($result['status'] != 9){
		for($i = 0; $i < count($problem_data['Subproblems']); $i ++){
			
			echo('<h5> 子題 ' . strval($i + 1) . '：</h5><br/>
			<div class="container"><div class="container"><table class="table" id="subproblem">
			<thead><tr><th>結果</th><th>輸入</th><th>輸出</th><th>耗時</th><th>記憶體</th><th>錯誤訊息</th></tr>
			</thead>
			<tbody>');
			foreach($result['Subproblems'][$i]['Testcases'] as $s){
				echo('<tr><td>'. array("Pending", "Compiling", "AC", "PE", "TLE", "MLE", "WA", "RE", "OLE", "CE", "SE", "NA")[$s['status']]. "</td>");
				if($problem_data['Subproblems'][$i]['Debugger'] AND !$problem_inCompetition){
					$s['Output'] = trim($s['Output']);
					if(strlen($s['Output']) >= 15){
						$s['Output'] = '<a href="/message?msg=' . $s['Output'] . '">' . substr($s['Output'], 0, 13) . "...</a>";
					}
				echo("<td>" . str_replace("\n","\\n",trim($s['Input'])) . "</td><td>" . str_replace("\n","\\n",$s['Output']));
				} else {echo('<td>不公開</td><td>不公開</td>');}
				echo("</td><td>" .  $s['Time'] . "ms</td><td>");
				if($s['Memory'] > 1){
					echo($s['Memory']); echo("mb");
				} else {
					echo($s['Memory'] * 1024); echo("kb");
				}
				if($problem_data['Subproblems'][$i]['Debugger'] AND !$problem_inCompetition){
				echo("</td><td>" . $s['Description']);
			    } else {
			    	echo("</td><td>不公開");
			    }
				echo("</td></tr>");
			}
			
			echo('</tbody></table></div></div>');	
			
		}
	}
?>
</div></div>
<hr/><br/><div class="container"><div class="container">
	<h5>代碼：<?php echo($solution[1]); ?></h5><br/>
<div class="container"><div class="container">
<textarea id="code" name="code" rows="10" class="form-control" style="margin: 0px -4px 0px 0px; width: 100%;"><?php
		$file = fopen("/var/www/judger/solutions/" .$id , "r");
		while (!feof($file)) {
		echo(fgets($file));
		}
	?></textarea>
</div></div></div></div>
<?php
include('/var/www/html/vendor/print_foot.php');
?>