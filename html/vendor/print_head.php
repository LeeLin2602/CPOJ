<?php
include_once("/var/www/html/account_process/validate.php");

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

  <!-- Custom styles for this template -->
  <link href="/css/modern-business.css" rel="stylesheet">

  <!-- LaTeX -->
  '); ?>

  <head>
    <script type="text/javascript" src="https://cdn.rawgit.com/codeassign/markjax/v1.1.2/dist/markjax.min.js"></script>
  </head>
  
<?php
echo('</head>

<body style="height: 100%;">

  <!-- Navigation -->
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
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
          </li>');
if(validate() == -1){ echo(
'
          <li class="nav-item">
            <a class="nav-link" href="/login">登入</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/register">註冊</a>
          </li>
            ');} else {
echo('          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownBlog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              '. strval(validate(1)) .'
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownBlog">
              <a class="dropdown-item" href="/status?uid=' . strval(validate()) . '">個人解題狀況</a>
              <a class="dropdown-item" href="/logout">登出</a>
            </div>
          </li>');
}

          echo('
        </ul>
      </div>
    </div>
  </nav>
  <br/>
  <!-- Page Content -->
  <div class="container" style="min-height:100%; padding-bottom:4em;"><div class="container" style="">
  ');
?>