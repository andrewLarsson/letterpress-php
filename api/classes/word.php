<?php
class Word {
	/*Contains all the variables and methods required to construct a token and authenticate it.*/

	/*Public Properties*/
	public $word;

	/*Public Methods*/
	function __construct($word = NULL) {
		if(isset($word)) {
			$this->word = $word;
		}
		return true;
	}

	function validate($word) {
		/*Validates a word with the dictionary.*/

		$file = substr($word, 0, 2) . ".txt";
		$handle = @fopen(SITE_ROOT . "/api/resources/dictionary/" . $file, "r");
		if($handle) {
			while(($buffer = fgets($handle, 4096)) !== false) {
				if($this->sanitize($word) == $this->sanitize($buffer)) {
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

	function sanitize($word) {
		/*Removes any invalid/dangerous characters from a supplied word.*/

		$newWord = strtolower($word);
		$newWord = preg_replace('/[^a-z]/', '', $newWord);
		return $newWord;
	}
}
?>
