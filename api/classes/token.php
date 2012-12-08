<?php
include_once "../config/db_config";

class Token {
	global $db;

	public $token;

	function __construct() {
		return true;
	}

	function register() {
		$this->token = md5(uniqid(rand(), true));
		$this->save();
		return true;
	}

	function save() {
		$query = "";
		mysql_query($query, $db);
	}
}
?>
