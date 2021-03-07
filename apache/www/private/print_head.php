<?php

echo('
<!DOCTYPE html>
<html>

<head> 
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="title" content="CPOJ">
  <meta name="description" content="竹北高中線上解題系統">
  <meta name="author" content="竹北高中軟體研究社">
  <link rel="icon" href="/icon.ico" type="image/x-icon" / >
  <link rel="shortcut icon" href="/icon.ico" type="image/x-icon" / >
  <!-- Bootstrap core CSS -->
  <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap core js -->
  <script src="/vendor/jquery/jquery.min.js"></script>
  <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Vue.js -->
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

  <!-- Custom styles for this template -->
  <link href="/css/modern-business.css" rel="stylesheet">

  <!-- Markdown and LaTeX -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script> -->
  <script type="text/javascript" src="https://cdn.rawgit.com/codeassign/markjax/v1.1.2/dist/markjax.min.js"></script>
</head>

<body style="height: 100%;">

  <!-- Navigation -->
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container" id="head">
      <a class="navbar-brand" href="/">CPOJ</a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="/listProblems">題庫</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/status">解題狀態</a>
          </li>
            
          <li class="nav-item" v-if="nologin">
            <a class="nav-link" href="/login">登入</a>
          </li>
          <li class="nav-item" v-if="nologin">
            <a class="nav-link" href="/register">註冊</a>
          </li>
            <li class="nav-item dropdown"  v-if="!nologin">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownBlog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{Name}}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownBlog">
              <a class="dropdown-item" v-bind:href="self">個人解題狀況</a>
              <a class="dropdown-item" v-on:click="logout">登出</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <br/>
  <script>
    var log = new Vue({
       el: "#head",
       data: {
          nologin: true,
          Name: "", 
          self: "/status?id=-1",
          id: -1
       },
       beforeMount(){
            axios.get("/api/verify.php")
            .then(response=>{
                this.nologin=false;
                /* console.log(response); */
                this.Name = response["data"]["data"]["Name"];
                this.ID = response["data"]["data"]["ID"];
                this.self = "/status?id=" + this.ID;
            })
            .catch(error=>{
                this.nologin=true;
            });
       },
       methods: {
            logout: function(){
                document.cookie = "userToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                location.href = "/login";
            }
       }
    });
  </script>
  <!-- Page Content -->
  <div class="container" style="min-height:100%; padding-bottom:4em;"><div class="container" style="">
  ');
?>