<?php include("/var/www/html/private/print_head.php") ?>

<style type="text/css">
    .ok {
        color: blue;
    }   
    .no {
        color: red;
    }
</style>

<div class="row">
    <div class="col-lg-8 mb-4">
      <h3>註冊帳號：</h3><br>
      <div id="loginform">
        <div class="control-group form-group">
            <label>使用者名稱：</label><span v-bind:class="p1">{{prompt1}}</span>
            <input type="text" class="form-control" v-model="userName" required="" data-validation-required-message="Please enter your name.">
        </div>
        <div class="control-group form-group">
          <div class="controls">
            <label>電子郵箱：</label><span v-bind:class="p2">{{prompt2}}</span>
            <input type="email" class="form-control" v-model="email" required="" data-validation-required-message="Please enter your email address.">
          </div>
        </div>
        <div class="control-group form-group">
          <div class="controls">
            <label>密碼：</label><span v-bind:class="p3">{{prompt3}}</span>
            <input type="password" class="form-control" v-model="password" name="password" required="" data-validation-required-message="Please enter your password.">
          </div>
        </div>
        <div class="control-group form-group">
          <div class="controls">
            <label>確認密碼：</label><span v-bind:class="p4">{{prompt4}}</span>
            <input type="password" class="form-control" v-model="pswordcheck" name="pswordcheck" required="" data-validation-required-message="Please enter your password again.">
          </div>
        </div>
        <p class="no">{{message}}</p>
        <br>
        <button class="btn btn-primary" v-bind:disabled="notcompleted" id="sendMessageButton" v-on:click="send">註冊</button><a href="/login" style="margin-left:1em">登入</a>
      </div>
    </div>
</div>


<script>

function validateEmail(value){
    try{
        return(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/.exec(value)[0] == value);
    }
    catch (e) {
        return(false);
    }
}

function validate(name, email, psd, psdcheck){
    return name.length >= 1 && name.length <= 20 && validateEmail(email) && psd.length >= 6 && psd == psdcheck;
}

var register = new Vue({
    el: '#loginform',
    data: {
        p1: '',
        p2: '',
        p3: '',
        p4: '',
        userName: '',
        email: '',
        password: '',
        pswordcheck: '',
        prompt1: '',
        prompt2: '',
        prompt3: '',
        prompt4: '',
        notcompleted: true,
        message: ''
    },
    watch: {
        userName: function(userName) {
            if(userName.length >= 21){
                this.p1 = 'no';
            } else if(userName.length == 0) {
                this.p1 = 'no';
            } else {
                this.p1 = 'ok';
            }
            this.prompt1 = userName.length + "/20";

            if(validate(this.userName, this.email, this.password, this.pswordcheck)) this.notcompleted = false;
            else this.notcompleted = true;
        },
        email: function(email){
            if(!validateEmail(email)){
                this.p2 = 'no';
                this.prompt2 = "請輸入合法的 Email。";
            } else {
                this.p1 = 'yes';
                this.prompt2 = "";
            }

            if(validate(this.userName, this.email, this.password, this.pswordcheck)) this.notcompleted = false;
            else this.notcompleted = true;
        },
        password: function(password){
            if(password.length < 6){
                this.p3 = 'no';
                this.prompt3 = "密碼需要至少六位。";
            } else {
                this.p3 = 'ok';
                this.prompt3 = "";
            }

            if(this.password == this.pswordcheck){
                this.p4 = 'ok';
                this.prompt4 = "";
            } else {
                this.p4 = 'no';
                this.prompt4 = "兩次密碼需要相同";
            }

            if(validate(this.userName, this.email, this.password, this.pswordcheck)) this.notcompleted = false;
            else this.notcompleted = true;
        },
        pswordcheck: function(psdc){
            if(this.password == psdc){
                this.p4 = 'ok';
                this.prompt4 = "";
            } else {
                this.p4 = 'no';
                this.prompt4 = "兩次密碼需要相同";
            }

            if(validate(this.userName, this.email, this.password, this.pswordcheck)) this.notcompleted = false;
            else this.notcompleted = true;
        }
    },
    methods: {
        send: function () {
            axios.post('/api/createUser.php', {
                Name: this.userName,
                Email: this.email,
                Password: this.password
            })
            .then(response=>{
                // console.log(response);
                location.href = '/login';
            })
            .catch(error=>{
                if(error.response.status >= 500){
                    this.message = '伺服器出現錯誤';
                } else if(error.response.status >= 400) {
                    this.message = '格式錯誤，請重新刷新頁面';
                }
                if(error.response.data.message != ""){
                    this.message = error.response.data.message;
                }
            })
        }
    }
});
</script>

<?php include("/var/www/html/private/print_foot.php") ?>
