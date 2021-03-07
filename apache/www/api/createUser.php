<?php

header('Content-Type: application/json');
include_once('/var/www/html/private/env.php');

if(empty($_POST)) $_POST = json_decode(file_get_contents('php://input'), true);

if($_SERVER["REQUEST_METHOD"] != "POST") {
    respond($code = 400, $msg = "Only POST allowed.");    
}

if(empty($_POST['Name']) OR empty($_POST['Password']) OR empty($_POST['Email'])){
    respond($code = 400, $msg = "Parameter missing.");
}

$sql = MySQL();

if($sql->connect_errno) {
    respond($code = 400, $msg = $sql->error);
}

$Name = $_POST['Name'];
$Password = password_hash($_POST['Password'], PASSWORD_ARGON2I);
$Email = $_POST['Email'];
$Date = date("Y/m/d");

if(mb_strlen($Name) > 20 OR $Name == "" OR (!ctype_alnum($Name)) OR mb_strlen($_POST['Password']) < 6 OR (!filter_var($Email, FILTER_VALIDATE_EMAIL))){
    respond($code = 400, $msg = "Invalid parameter.");
}

$checkName = $sql->prepare("SELECT `Name` FROM Accounts WHERE `Name` = ?");
$checkName->bind_param('s', $Name);
$checkName->execute();
if($checkName->errno) respond($code = 500, $msg = $checkName->error);
$checkName->store_result();
if($checkName->num_rows != 0){
    respond($code = 400, $msg = "帳號已經被註冊過了。");
}

$checkEmail = $sql->prepare("SELECT `Email` FROM Accounts WHERE `Email` = ?");
$checkEmail->bind_param('s', $Email);
$checkEmail->execute();
if($checkEmail->errno) respond($code = 500, $msg = $checkEmail->error);
$checkEmail->store_result();
if($checkEmail->num_rows != 0){
    respond($code = 400, $msg = "信箱已經被註冊過了。");
}

$stmt = $sql->prepare("INSERT INTO Accounts (`Name`, `Password`, `Email`, `Birthday`) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $Name, $Password, $Email, $Date);
$stmt->execute();

if($stmt->errno) {
    respond($code = 500, $msg = $stmt->error);
}

respond($code = 201);

?>
