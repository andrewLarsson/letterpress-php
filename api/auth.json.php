<?php
include_once "classes/token.php";
include_once "config/config.php";
include_once "lib/database.php";
include_once "lib/returnJSON.php";

action();
finish();

function action() {
	global $token;

	if(isset($_REQUEST['register'])) {
		if(getNewToken()) {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "There was a problem creating a new authentication token.";
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "Your unique authentication token has been registered.";
			$returnStatement['data']['token'] = $token;
			returnJSON($returnStatement);
		}
	} else if(isset($_REQUEST['authenticate'])) {
		if(checkAuth()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "The authentication token is valid.";
			$returnStatement['data']['token'] = $token;
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "The authentication token is either missing or invalid.";
			returnJSON($returnStatement);
		}
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "No action specified.";
		returnJSON($returnStatement);
	}
}

function getNewToken() {
	global $token;

	$tokenObj = new Token();
	if(!$tokenObj->register()){
		return false;
	}
	$token = $tokenObj->token;
	return true;
}

function checkAuth() {
	global $token;

	if(!isset($_REQUEST['token'])) {
		return false;
	}
	$token = $_REQUEST['token'];
	$tokenObj = new Token($token);
	if(!$tokenObj->authenticate()) {
		return false;
	}
	return true;
}

function finish() {
	global $db;

	mysql_close($db);
}
?>
