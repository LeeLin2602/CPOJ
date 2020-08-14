<?php
session_start();
function validate($get = 0){
	if(isset($_COOKIE['UID'])){
		$UID = $_COOKIE['UID'];
		if(isset($_SESSION[$UID])){
			if($get == 0){
				return $_SESSION[$UID];
			} return $_SESSION[$UID . "Name"];
		} else {
			setcookie("UID", "", time());
			return -1;
		}
	} return -1;
}
?>