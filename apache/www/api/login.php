<?php

header('Content-Type: application/json');
include_once("/var/www/html/private/env.php");

if(empty($_POST)) $_POST = json_decode(file_get_contents('php://input'), true);

if($_SERVER["REQUEST_METHOD"] != "POST") {
    respond($code = 400, $msg = "Only POST allowed.");    
}

if(empty($_POST['Email']) OR empty($_POST['Password'])){
    respond($code = 400, $msg = "Parameter missing.");
}

$sql = MySQL();
$Email = $_POST['Email'];
$Password = $_POST['Password'];

if(!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
    respond($code = 400, $msg = "Invalid parameter.");
}

$query = $sql->prepare("SELECT * FROM Accounts WHERE `Email` = ?");
$query->bind_param('s', $Email);
$query->execute();
if($query->errno) respond($code = 500, $msg = $query->errno);
/* $query->store_result(); */
$result = $query->get_result();

if($result->num_rows == 0){
    respond($code = 401);
}

$result = $result->fetch_assoc();
$passwd = $result['Password'];

if(!password_verify($Password, $passwd)) respond($code = 401);

$payload = array(
    'iss'=>'cpoj',
    'iat'=>time(),
    'exp'=>time() + 24 * 60 * 60 * 7 ,
    'ID'=>$result['ID'],
    'Name'=>$result['Name'],
    'auth'=>$result['auth']
);

$jwt = new jwtAuth;
$token = $jwt::getToken($payload);

setcookie("userToken", $token, time() + 24 * 60 * 60 * 7, "/");

respond($code = 200, $msg = "", $data = array("token"=>$token));

?>
