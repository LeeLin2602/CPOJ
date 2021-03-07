<?php include("/var/www/html/private/print_head.php") ?>
<!--
<div id="loginform">
    信箱：<input v-model="email"><br/>
    密碼：<input v-model="password" type="password"><br/>
    <p>{{message}}</p>
    <button v-on:click="send">登入</button>
</div>
-->
<div class="row">
    <div class="col-lg-8 mb-4">
      <h3>登入帳號：</h3><br>
      <div id="loginform">
        <div class="control-group form-group">
          <div class="controls">
            <label>電子郵箱：</label>
            <input type="email" class="form-control" v-model="email" required="" data-validation-required-message="Please enter your email address.">

          </div>
        </div>
        <div class="control-group form-group">
          <div class="controls">
            <label>密碼：</label>
            <input type="password" class="form-control" v-model="password" name="password" required="" data-validation-required-message="Please enter your password.">
          </div>
        </div><p>{{message}}</p><br>
        <button class="btn btn-primary" id="sendMessageButton" v-on:click="send">登入</button><a href="/register" style="margin-left:1em">註冊</a>
      </div>
    </div>
</div>


<script>
var login = new Vue({
    el: '#loginform',
    data: {
        email: '',
        password: '',
        message: ''
    },
    methods: {
        send: function () {
            axios.post('/api/login.php', {
                Email: this.email,
                Password: this.password
            })
            .then(response=>{
                // console.log(response); 
                location.href = '/';
            })
            .catch(error=>{
                if(error.response.status >= 500){
                    this.message = '伺服器出現錯誤';
                } else if(error.response.status >= 400) {
                    this.message = '帳號或密碼錯誤';
                }
                // console.log(error.response.data);
            })
        }
    }
});
</script>

<?php include("/var/www/html/private/print_foot.php") ?>
