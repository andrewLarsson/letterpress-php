<?php
class Token {
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
		global $db;
		$query = "INSERT INTO tokens (token) VALUES ('{$this->token}');";
		mysql_query($query, $db);
	}
}
?>
