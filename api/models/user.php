<?php
class User {
	/*Contains all the variables and methods required to construct a user and authenticate it with a token.*/

	//A user is represented in MySQL as: id, token (md5), username (32).

	/*Public Properties*/
	public $token;
	public $username;

	/*Constructor*/
	function __construct($token = NULL) {
		if(isset($token)) {
			$this->token = $token;
			if(!$this->load()) {
				throw(new Exception());
			}
		}
	}

	/*Public Methods*/
	public function register($username) {
		/*Creates a new user and saves it to the database.*/

		$this->token = md5(uniqid(rand(), true));
		$this->username = $username;
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	/*Private Functions*/
	public function load() {
		/*Loads a user from the database with a token.*/

		global $db;

		if(!$db) {
			return false;
		}
		$query = "SELECT * FROM users WHERE token='" . mysql_real_escape_string($this->token) . "'";
		if(!$result = mysql_query($query, $db)) {
			return false;
		}
		if(!$row = mysql_fetch_array($result)) {
			return false;
		}
		$this->username = $row['username'];
		return true;
	}

	private function save() {
		/*Writes the user to the database.*/

		global $db;

		if(!$db) {
			return false;
		}
		$query = "INSERT INTO users (token, username) VALUES ('" . mysql_real_escape_string($this->token) . "', '" . mysql_real_escape_string($this->username) . "')";
		if(!mysql_query($query, $db)) {
			return false;
		}
		return true;
	}
}
?>
