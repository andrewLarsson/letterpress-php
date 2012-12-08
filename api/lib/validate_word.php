<?php
include_once "../config/config.php";

function validate_word($word) {
	$file = substr($word, 0, 2) . ".txt";
	$handle = @fopen(SITE_ROOT . "/api/resources/dictionary/" . $file, "r");
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			if (sanitize_word($word) == sanitize_word($buffer)) {
				return true;
			}
		}
		if (!feof($handle)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($handle);
	} else {
		return false;
	}
}

function sanitize_word($word) {
	$newWord = strtolower($word);
	$newWord = preg_replace('/[^a-z]/', '', $newWord);
	
	return $newWord;
}
?>