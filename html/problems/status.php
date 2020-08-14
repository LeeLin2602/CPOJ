<?php 
	include('/var/www/html/vendor/print_head.php');
	include("/var/www/interfaces/mysql.php");
	$sql = new MySQL();

	$p = 1; $pid = -1; $uid = -1;
	if(isset($_GET['p']) and is_numeric($_GET['p'])){
		$p = intval($_GET['p']);}
		echo('<meta name="robots" content="noindex" />');
	if(isset($_GET['pid']) and is_numeric($_GET['pid'])){
		$pid = intval($_GET['pid']);
		echo('<meta name="robots" content="noindex" />');
	}
	if(isset($_GET['uid']) and is_numeric($_GET['uid'])){
		$uid = intval($_GET['uid']);
		echo('<meta name="robots" content="noindex" />');
	}

	if($pid != -1){
		echo('<a href="/showProblem?id=' . strval($pid) .'">回題目</a><br/><br/>');
	} 


	if($uid != -1){
		$user = $sql -> select("Accounts", "ID = $uid") -> fetch_array();
		$Name = $user['Name'];
		echo("<br/><h5>$Name 的解題狀態：</h5><br/>通過率：" . $user['AC_Times'] . "/" . $user['Submit_Times']);
		$result = $sql -> select("Problems", "isPublic = 1");
		$Problems = array();
		while($row = $result -> fetch_array()){
			$Problems[$row['ID']] = 0;
		}
		$result = $sql -> select("Static", "Submitter = $uid");
		while($row = $result -> fetch_array()){
			if(array_key_exists($row['ProblemID'], $Problems)){
				$Problems[$row['ProblemID']] = $row['Status'];
			}
		}
		echo('<br/><br/>解題記錄：<br/><br/><div class="container" id="SolvBoard">');
		foreach ($Problems as $key => $value) {
			echo('<a href="/showProblem?id=' . $key.'" style="color:' . ($value==2?"#18B202":($value==0?"#535353":"#535353;font-weight:bold;")) .';">' .$key.'</a>&emsp;');
		}
		echo("</div><br/><hr/><br/>最近提交動態：<br/>");
	}
?>
<br/><br/>
<title>CPOJ 解題動態 - 第 <?php echo($p); ?> 頁</title>
<table class="table">
<thead><tr><th style="text-align:center">提交編號</th><th>提交者</th><th>題目</th><th>語言</th><th>結果</th><th>上傳時間</th></tr>
</thead>
<tbody>
<?php

if($pid == -1 and $uid == -1){
	$result = $sql -> select("Solutions", "", "ID DESC", 20, 20 * ($p - 1));
} else if($pid == -1){
	$result = $sql -> select("Solutions", "Submitter=" . strval($uid), "ID DESC", 20, 20 * ($p - 1));
}else if($uid == -1){
	$result = $sql -> select("Solutions", "ProblemID=" . strval($pid), "ID DESC", 20, 20 * ($p - 1));
} else {
	$result = $sql -> select("Solutions", "ProblemID=" . strval($pid) . " AND Submitter=" . strval($uid), "ID DESC", 20, 20 * ($p - 1));

}
$num = $result -> num_rows;
$permission = $sql -> select("Accounts", "auth = 1 AND ID = " . strval(validate())) -> num_rows;
while($row = $result -> fetch_array()){
	echo('<tr><td style="text-align:center">'.$row['ID'].'</td><td>');
	echo((($sql -> select("Accounts", "ID = ". $row['Submitter'])) -> fetch_array())['Name'] . '</td><td>');
	echo('<a href="/showProblem?id=' . $row['ProblemID'] . '">' . (($sql -> select("Problems", "ID = ". $row['ProblemID'])) -> fetch_array())['Title'] . '</a></td><td>' . $row['Language'] . '</td><td>');
	if(($permission or $row['Submitter'] == validate()) and $row['Status'] > 1){
		echo('<a href="/viewResult?id=' . $row['ID'] .'">');
	}
	if($row['Status'] == 0 or $row['Status'] == 12){
		echo('<font style="color:orange"> Pending </font>');
	} elseif($row['Status'] == 1){
		echo('<font style="color:blue"> Compiling </font>');
	} elseif($row['Status'] == 2){
		echo('<font style="color:green"> AC </font>');
	} elseif($row['Status'] == 3){
		echo('<font style="color:red"> PE </font<');
	} elseif($row['Status'] == 4){
		echo('<font style="color:red"> TLE </font>');
	} elseif($row['Status'] == 5){
		echo('<font style="color:red"> MLE </font>');
	} elseif($row['Status'] == 6){
		echo('<font style="color:red"> WA </font>');
	} elseif($row['Status'] == 7){
		echo('<font style="color:red"> RE </font>');
	} elseif($row['Status'] == 8){
		echo('<font style="color:red"> OLE </font>');
	} elseif($row['Status'] == 9){
		echo('<font style="color:red"> CE </font>');
	} elseif($row['Status'] == 10){
		echo('<font style="color:red"> SE </font>');
	} elseif($row['Status'] == 11){
		echo('<font style="color:red"> NA </font>');
	} 
	if($row['Submitter'] == validate()){
		echo('</a>');
	}
	echo('</td><td> ' . $row['Upload_time']. '</td></tr>');

}
?>
</tbody>
</table>

<?php 

echo("第 " . $p . " 頁 ");

if($p != 1 or $num == 20){
	echo("｜ 《");
}

if($p != 1){
	echo('<a href="/status?p=' . strval($p - 1) . '&pid=' . strval($pid) . '&uid=' . strval($uid) .'">上一頁</a>');
}

if($num == 20){
	if($p != 1) {echo("|");}
	echo('<a href="/status?p=' . strval($p + 1) . '&pid=' . strval($pid) .'&uid=' .strval($uid) . '">下一頁</a>');
}

if($p != 1 or $num == 20){
echo("》");
}
include('/var/www/html/vendor/print_foot.php');
?>