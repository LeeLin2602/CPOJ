<?php include('/var/www/html/vendor/print_head.php');?>
<title>CPOJ 竹北高中線上解題系統</title>
<div style="width: 100%;word-break: break-all;">
<a href onclick="history.go(-1)">回上一頁</a><br/><br/>

<?php 
if(isset($_GET['msg'])){
	echo($_GET['msg']); 
}
?>
</div>
<?php include('/var/www/html/vendor/print_foot.php');?>