<?php
include_once "classes/token.php";
include_once "config/config.php";
include_once "lib/database.php";
include_once "lib/json.php";

action();
finish();

function action() {
	global $token;

	if(isset($_REQUEST['register'])) {
		if(getNewToken()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "Your unique authentication token has been registered.";
			$returnStatement['data']['token'] = $token->id;
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "There was a problem creating a new authentication token.";
			returnJSON($returnStatement);
		}
	} else if(isset($_REQUEST['authenticate'])) {
		if(checkAuth()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "The authentication token is valid.";
			$returnStatement['data']['token'] = $token->id;
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

	$token = new Token();
	if(!$token->register()){
		return false;
	}
	return true;
}

function checkAuth() {
	global $token;

	if(!isset($_REQUEST['token'])) {
		return false;
	}
	$token = new Token($_REQUEST['token']);
	if(!$token->authenticate()) {
		return false;
	}
	return true;
}

function finish() {
	global $db;

	mysql_close($db);
}
?>
