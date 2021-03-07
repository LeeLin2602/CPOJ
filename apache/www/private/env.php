<?php

$MYSQL_HOST     = '172.19.0.1';
$MYSQL_USER     = 'judge';
$MYSQL_PASSWD   = 'a84dc85b974';
$MYSQL_DB       = 'oj';

include_once("/var/www/html/private/jwtAuth.php");

function MySQL(){
    global $MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWD, $MYSQL_DB;
    return new mysqli(
        $MYSQL_HOST, 
        $MYSQL_USER, 
        $MYSQL_PASSWD, 
        $MYSQL_DB);
}

function respond($code = 200, $msg = "Ok.", $data = array()){
    /* header("status: $code"); */
    http_response_code($code);
    if($code >= 300 and $msg == "Ok.") $msg = "";
    
    $response = array(
        "message" => $msg,
        "data" => $data
    );

    echo(json_encode($response));
    exit();
}

function verify(){
    if(!isset($_COOKIE['userToken'])) return false;
    $token = $_COOKIE['userToken'];
    $payload = jwtAuth::verifyToken($token);
    if($payload === false) return false;
    return $payload;
}

?>
