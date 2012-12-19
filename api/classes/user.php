<?php
class User {
	/*Contains all the variables and methods required to construct a token and authenticate it.*/

	/*Public Properties*/
	public $token;

	/*Public Methods*/
	function __construct($token = NULL) {
		if(isset($token)) {
			$this->token = $token;
		}
		return true;
	}

	function register() {
		/*Creates a new token and saves it to the database.*/

		$this->token = md5(uniqid(rand(), true));
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	function authenticate() {
		/*Checks to make sure the token is valid.*/

		global $db;

		if(!$db) {
			return false;
		}
		if(!isset($this->token)) {
			return false;
		}
		$query = "SELECT token FROM users WHERE token='" . $this->token . "';";
		if(!mysql_fetch_array(mysql_query($query, $db))) {
			return false;
		}
		return true;
	}

	/*Private Functions*/
	private function save() {
		/*Writes the token to the database.*/

		global $db;

		if(!$db) {
			return false;
		}
		$query = "INSERT INTO users (token) VALUES ('" . $this->token . "');";
		if(!mysql_query($query, $db)) {
			return false;
		}
		return true;
	}
}
?>
