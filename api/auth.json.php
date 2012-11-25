<?php
include_once "classes/token.php";
include_once "config/config.php";
include_once "lib/database.php";

global $token;

if(isset($_REQUEST['register'])) {
	getNewToken();
} else {
	$returnStatement['status'] = 1;
	$returnStatement['error'] = "No action specified.";
	returnJSON($returnStatement);
}

function getNewToken() {
	global $token;

	$tokenObj = new Token();
	if($tokenObj->register()){
		$token = $tokenObj->token;
		$returnStatement['status'] = 0;
		$returnStatement['token'] = $token;
		returnJSON($returnStatement);
	}
}

function checkAuth() {
	global $token;

	if(isset($_REQUEST['token'])) {
		$token = $_REQUEST['token'];
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['error'] = "The authentication token you provided was either missing or invalid.";
		returnJSON($returnStatement);
		return false;
	}
	return true;
}

function returnJSON($JSON) {
	echo json_encode($JSON);
}
?>
