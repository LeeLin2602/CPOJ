<?php 
	$p = 1;

	if(isset($_GET['p']) and is_numeric($_GET['p'])){$p = intval($_GET['p']);}


	include('/var/www/html/vendor/print_head.php');
?>

<title>CPOJ 題庫</title>
<table class="table">
	<thead><tr><th style="text-align:center">問題編號</th><th>題目名稱</th><th>題目標籤</th><th>題目來源</th><th>通過率</th></tr></thead>
	<tbody>
		<?php
			include('/var/www/interfaces/mysql.php');
			$sql = new MySQL();
			$result = $sql -> select("Problems", "isPublic = 1", "ID DESC", 20, 20 * ($p - 1));
			$num = $result -> num_rows;
			while($row = $result -> fetch_array()){
				$href = '<a href="/showProblem?id=' . $row['ID'] .  '">';
				echo('<tr><td style="text-align:center">' . $href . $row['ID'] . '</a></td>');
				echo('<td>' . $href . $row['Title'] . '</a></td>');
				$Tags = array("入門","簡單", "中等", "進階","困難")[$row['Difficulty']];
				$TagsResult = $sql -> select("Tags", "ProblemID = " . $row['ID']);
				while($Tag = $TagsResult -> fetch_array()){
					$Tag = $sql -> select("TagsName", "ID = " . $Tag["KeyID"]) -> fetch_array()['KeyName'];
					if($Tags == ""){
						$Tags = $Tag;
					} else {
						$Tags .= ", " . $Tag;
					}
				}
				echo('<td>' . $Tags . '</td><td>' . $row['Source'] . '</td>');
				echo('<td>' . $row['AC_Times'] . '/' . $row['Submit_Times'] . '</td></tr>');
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
	echo('<a href="/listProblems?p=' . strval($p - 1) . '">上一頁</a>');
}

if($num == 20){
	if($p != 1) {echo("|");}
	echo('<a href="/listProblems?p=' . strval($p + 1) . '">下一頁</a>');
}

if($p != 1 or $num == 20){
echo("》");
}

include('/var/www/html/vendor/print_foot.php');?>