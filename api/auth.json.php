<?php
include_once "classes/user.php";
include_once "config/config.php";
include_once "lib/database.php";
include_once "lib/json.php";

action();
finish();

function action() {
	global $user;
	global $returnStatement;

	if(isset($_REQUEST['register'])) {
		if(createNewUser()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "You have been registered.";
			$returnStatement['data']['user']['token'] = $user->token;
			$returnStatement['data']['user']['username'] = $user->username;
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "There was a problem creating a new user.";
		}
	} else if(isset($_REQUEST['authenticate'])) {
		if(checkAuth()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "The authentication token is valid.";
			$returnStatement['data']['user']['token'] = $user->token;
			$returnStatement['data']['user']['username'] = $user->username;
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "The authentication token is either missing or invalid.";
		}
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "There was no action specified.";
	}
}

function createNewUser() {
	global $user;

	if(!isset($_REQUEST['username'])) {
		return false;
	}
	try {
		$user = new User();
	} catch(Exception $e) {
		return false;
	}
	if(!$user->register($_REQUEST['username'])) {
		return false;
	}
	return true;
}

function checkAuth() {
	global $user;

	if(!isset($_REQUEST['token'])) {
		return false;
	}
	try {
		$user = new User($_REQUEST['token']);
	} catch(Exception $e) {
		return false;
	}
	return true;
}

function finish() {
	global $db;
	global $returnStatement;

	mysql_close($db);
	returnJSON($returnStatement);
}
?>
