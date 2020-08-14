<?php 
include "/var/www/html/vendor/print_head.php";
if(isset($_GET['back'])){
  echo('<meta name="robots" content="noindex" />');
}
?>



<head>
  <title>CPOJ 竹北高中線上解題系統 - 登入</title>
</head>

<div class="row">
    <div class="col-lg-8 mb-4">
      <h3>登入帳號：</h3><br/>
      <form name="info" action="/log<?php if(isset($_GET['back'])){echo('?back='.htmlspecialchars($_GET['back']));}?>" method="POST" id="info">
        <div class="control-group form-group">
          <div class="controls">
            <label>電子郵箱：</label>
            <input type="email" class="form-control" id="email" name="email" required data-validation-required-message="Please enter your email address.">

          </div>
        </div>
        <div class="control-group form-group">
          <div class="controls">
            <label>密碼：</label>
            <input type="password" class="form-control" id="password" name="password" required data-validation-required-message="Please enter your password.">
          </div>
        </div>
        <?php 
          if(isset($_GET['status'])){
            echo('<span style="color:red;">' . htmlspecialchars($_GET['status']) . '</span>');
          }
        ?>
        <br/>
        <button class="btn btn-primary" id="sendMessageButton" type="submit">登入</button>&emsp;&emsp;<a href="/register">註冊</a>
      </form>
    </div>
</div>
<?php include "/var/www/html/vendor/print_foot.php"?>