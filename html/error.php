<?php include('/var/www/html/vendor/print_head.php');?>
<?php 
<?php
switch($_SERVER["REDIRECT_STATUS"]){
  case 500:
    $title = "500: Internal Server Error";
    $description = "伺服器內部錯誤。";
  break;
  case 502:
    $title = "502: Bad Gateway";
    $description = "閘道器錯誤。";
  break;
  case 504:
    $title = "504: Gateway Timeout";
    $description = "連線超時。";
  break;
}
echo("<title>CPOJ - ". $_SERVER["REDIRECT_STATUS"] . "</title>");
?>
<h1>ERROR<?php echo($title); ?></h1><hr><br/><br/><?php echo($description);?>
<div style="text-align: center;"><a href="/" class="btn btn-primary">回首頁</a></div>
<?php include('/var/www/html/vendor/print_foot.php');?>