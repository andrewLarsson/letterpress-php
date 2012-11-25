<?php
class Token {
	public $token;

	function __construct($inputToken = NULL) {
		if(isset($inputToken)) {
			$this->token = $inputToken;
		}
		return true;
	}

	function register() {
		$this->token = md5(uniqid(rand(), true));
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	function save() {
		global $db;

		if(!$db) {
			return false;
		}
		$query = "INSERT INTO tokens (token) VALUES ('{$this->token}');";
		mysql_query($query, $db);
		return true;
	}

	function authenticate() {
		global $db;

		if(!$db) {
			return false;
		}
		if(!isset($this->token)) {
			return false;
		}
		$query = "SELECT token FROM tokens WHERE token='{$this->token}';";
		if(!mysql_fetch_array(mysql_query($query, $db))) {
			return false;
		}
		return true;
	}
}
?>
