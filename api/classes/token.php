<?php
class Token {
	/*Contains all the variables and methods required to construct a token and authenticate it.*/

	/*Public Properties*/
	public $id;

	/*Public Methods*/
	function __construct($id = NULL) {
		if(isset($id)) {
			$this->id = $id;
		}
		return true;
	}

	function register() {
		/*Creates a new token and saves it to the database.*/

		$this->id = md5(uniqid(rand(), true));
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
		if(!isset($this->id)) {
			return false;
		}
		$query = "SELECT token FROM tokens WHERE token='" . $this->id . "';";
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
		$query = "INSERT INTO tokens (token) VALUES ('" . $this->id . "');";
		if(!mysql_query($query, $db)) {
			return false;
		}
		return true;
	}
}
?>
