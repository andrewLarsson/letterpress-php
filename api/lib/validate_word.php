<?php
class Word {
	public $word;

	function __construct($word = NULL) {
		if(isset($word)) {
			$this->word = $word;
		}
		return true;
	}

	function validate($word) {
		$file = substr($word, 0, 2) . ".txt";
		$handle = @fopen(SITE_ROOT . "/api/resources/dictionary/" . $file, "r");
		if($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				if ($this->sanitize($word) == $this->sanitize($buffer)) {
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
		$newWord = strtolower($word);
		$newWord = preg_replace('/[^a-z]/', '', $newWord);
		return $newWord;
	}
}
?>
