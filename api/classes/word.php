<?php
class Word {
	/*Contains all the variables and methods required to construct a token and authenticate it.*/

	/*Public Properties*/
	public $word;

	/*Private Variables*/
	private $valid;

	/*Public Methods*/
	function __construct($word = NULL) {
		if(isset($word)) {
			$this->word = $word;
		}
		return true;
	}

	function validate($word) {
		/*Validates a word with the dictionary.*/

		if(isset($this->valid)) {
			return $this->valid;
		}
		$file = substr($word, 0, 2) . ".txt";
		$handle = @fopen(SITE_ROOT . "/api/resources/dictionary/" . $file, "r");
		if($handle) {
			while(($buffer = fgets($handle)) !== false) {
				if($this->sanitize($word) == $this->sanitize($buffer)) {
					return $this->valid = true;
				}
			}
			if(!feof($handle)) {
				return $this->valid = false;
			}
			fclose($handle);
		} else {
			return $this->valid = false;
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
