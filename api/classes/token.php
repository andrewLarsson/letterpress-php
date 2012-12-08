<?php
class authToken() {
	public $token;

	function register() {
		$this->token = md5(uniqid(rand(), true));
	}
}
?>
