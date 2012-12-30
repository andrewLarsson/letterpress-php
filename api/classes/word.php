<?php
class Word {
	/*Contains all the variables and methods required to construct a token and authenticate it.*/
	
	/*Public Properties*/
	public $wordString;
	
	/*Constructor*/
	function __construct($string = NULL) {
		if(isset($string)) {
			$this->wordString = $string;
		}
	}

	public function validate() {
		/*Validates a word with the dictionary.*/
				
		$file = substr($this->wordString, 0, 2) . ".txt";
		$handle = @fopen(SITE_ROOT . "/api/resources/dictionary/" . $file, "r");
		if($handle) {
			while(($buffer = fgets($handle)) !== false) {
				if($this->sanitize($this->wordString) == $this->sanitize($buffer)) {
					return true;
				}
			}
			if(!feof($handle)) {
				return false;
			}
			fclose($handle);
		} else {
			return false;
		}
	}
	
	/*Private Functions*/
	private function sanitize($string) {
		/*Removes any invalid/dangerous characters from a supplied word.*/
		
		$newWord = strtolower($string);
		$newWord = preg_replace('/[^a-z]/', '', $newWord);
		return $newWord;
	}
}
?>
