<?
header('Content-Type: application/json');
include_once("/var/www/html/private/env.php");
if(!isset($_COOKIE['userToken'])) respond(401);
$token = $_COOKIE['userToken'];
$payload = jwtAuth::verifyToken($token);
if($payload === false) respond(401);
respond(200, "", $payload);
?>
