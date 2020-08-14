<?php
session_start();
unset($_SESSION[$UID . "Name"]);
unset($_SESSION[$UID]);
setcookie("UID", "", time());
header("location: /");
?>