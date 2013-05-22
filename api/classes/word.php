<?php
class Word {
	/*Contains all the variables and methods required to construct a token and authenticate it.*/

	/*Public Properties*/
	private $wordString;

	/*Constructor*/
	function __construct($string = NULL) {
		if(isset($string)) {
			$this->wordString = $this->sanitize($string);
		}
	}

	/*Public Methods*/
	public function validate() {
		/*Validates a word with the dictionary.*/

		if(!$handle = @fopen(SITE_ROOT . "/api/resources/dictionary/" . substr($this->wordString, 0, 2) . ".txt", "r")) {
			return false;
		}
		while(($buffer = fgets($handle)) !== false) {
			if($this->wordString == $this->sanitize($buffer)) {
				fclose($handle);
				return true;
			}
		}
		fclose($handle);
		return false;
	}

	/*Private Functions*/
	private function sanitize($string) {
		/*Removes any invalid/dangerous characters from a supplied word.*/

		return preg_replace('/[^a-z]/', '', strtolower($string));
	}
}
?>
