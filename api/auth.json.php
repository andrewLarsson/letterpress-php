<?php
include_once "classes/user.php";
include_once "config/config.php";
include_once "lib/database.php";
include_once "lib/json.php";

action();
finish();

function action() {
	global $user;

	if(isset($_REQUEST['register'])) {
		if(getNewToken()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "You have been registered.";
			$returnStatement['data']['user']['token'] = $user->token;
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "There was a problem creating a new user.";
			returnJSON($returnStatement);
		}
	} else if(isset($_REQUEST['authenticate'])) {
		if(checkAuth()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "The authentication token is valid.";
			$returnStatement['data']['user']['token'] = $user->token;
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
	global $user;

	$user = new User();
	if(!$user->register()){
		return false;
	}
	return true;
}

function checkAuth() {
	global $user;

	if(!isset($_REQUEST['token'])) {
		return false;
	}
	$user = new User($_REQUEST['token']);
	if(!$user->authenticate()) {
		return false;
	}
	return true;
}

function finish() {
	global $db;

	mysql_close($db);
}
?>
