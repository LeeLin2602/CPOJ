<?php include "/var/www/html/vendor/print_head.php"?>
<head>

  <title>CPOJ 竹北高中線上解題系統 - 註冊</title>
  <script type="text/javascript">
    function change_name(){
      var name = document.getElementById("name").value;
      if(name != ""){
        if(!validateName(name)){
            if(name.length<=11){
              document.getElementById("name_box").innerHTML = '<span style="color:red;">非法字符</span>';
              return;
            } else {
            document.getElementById("name_box").innerHTML = '<span style="color:red;">' +name.length.toString() + "/11</span>";
            }
            
        } else {
          document.getElementById("name_box").innerHTML = '<span style="color:blue;">' +name.length.toString() + "/11</span>";
        }
      } else {
        document.getElementById("name_box").innerHTML = '<span style="color:red;">請輸入名稱</span>';
      }
    }

    function change_email(){
      var email = document.getElementById("email").value;
      if(email == ""){
        document.getElementById("email_box").innerHTML = '<span style="color:red;">請輸入電子郵箱</span>';
      } else {
        if(!validateEmail(email)){
          document.getElementById("email_box").innerHTML = '<span style="color:red;">電子郵箱不合法</span>';
        } else {
          document.getElementById("email_box").innerHTML = '<span style="color:blue;">OK!</span>';
        }
      }
    }
    function change_password(){
      var password = document.getElementById("password").value;
      if(password == ""){
        document.getElementById("password1_box").innerHTML = '<span style="color:red;">請輸入密碼</span>';
      }
      if(password.length < 6){
          document.getElementById("password1_box").innerHTML = '<span style="color:red;">密碼至少 6 位</span>';
      } else {
          document.getElementById("password1_box").innerHTML = '<span style="color:blue;">OK!</span>';
      }
    }
    function change_password2(){
      var password = document.getElementById("password").value;
      var password2 = document.getElementById("password2").value;
      if(password == password2){
        document.getElementById("password2_box").innerHTML = '<span style="color:blue;">OK!</span>';
      } else {
          document.getElementById("password2_box").innerHTML = '<span style="color:red;">兩次密碼必須相同</span>';
      }
    }
    function validateName(value){
      try {
        return(/^[\u4e00-\u9fa5A-Za-z0-9_]{1,11}/.exec(value)[0] == value);
      }
      catch (e) {
        return(false);
      }
    }
    function validateEmail(value){
      try{
      return(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/.exec(value)[0] == value);
          }
      catch (e) {
        return(false);
      }
    }
    function validatePasswd(value1, value2){
      return(value1.length >= 6 && value1 == value2)
    }
    function validateForm(form){
      if (validateName(form.name.value) && validateEmail(form.email.value) && validatePasswd(form.password.value, form.password2.value)){
            document.getElementById("sendMessageButton").disabled = false;
          }
    }
  </script>
</head>
<div class="row">
    <div class="col-lg-8 mb-4">
      <h3>註冊帳號：</h3><br/>
      <form name="info" action="/reg" method="POST" id="info">
        <div class="control-group form-group">
          <div class="controls">
            <label>使用者名稱：</label><span id="name_box"></span>
            <input type="text" oninput="change_name();validateForm(this.form)" class="form-control" id="name" name="name" required data-validation-required-message="Please enter your name.">
            
          </div>
        </div>
        <div class="control-group form-group">
          <div class="controls">
            <label>電子郵箱：</label><span id="email_box"></span>
            <input type="email" oninput="change_email();validateForm(this.form)" class="form-control" id="email" name="email" required data-validation-required-message="Please enter your email address.">

          </div>
        </div>
        <div class="control-group form-group">
          <div class="controls">
            <label>密碼：</label><span id="password1_box"></span>
            <input type="password" oninput="change_password();change_password2();validateForm(this.form);" class="form-control" id="password" name="password" required data-validation-required-message="Please enter your password.">
          </div>
        </div>
        <div class="control-group form-group">
          <div class="controls">
            <label>確認密碼：</label><span id="password2_box"></span>
            <input type="password" oninput="change_password2();validateForm(this.form)" class="form-control" id="password2" required data-validation-required-message="Please comfirm your password.">
          </div>
        </div>
        <?php 
          if(isset($_GET['status'])){
            echo('<span style="color:red;">' . htmlspecialchars($_GET['status']) . '</span>');
          }
        ?>
        <br/>
        <button class="btn btn-primary" disabled id="sendMessageButton">創建帳號</button>&emsp;&emsp;<a href="/login">登入</a>
      </form>
    </div>
</div>
<?php include "/var/www/html/vendor/print_foot.php"?>